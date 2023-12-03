<?php

namespace App\Http\Controllers;

use App\Helpers\getUserData;
use App\Helpers\HasEnsure;
use App\Helpers\fileTrait;
use App\Models\Component;
use App\Models\ComponentProductionSchema;
use App\Models\Instruction;
use App\Models\Product;
use App\Models\ProductComponent;
use App\Models\ProductionSchema;
use App\Models\ProductionStandard;
use App\Models\StaticValue;
use App\Models\Unit;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpParser\Node\Expr\Cast\Double;
use stdClass;

class ProductController
{
    use HasEnsure;

    /**
     * Display the employee dashboard.
     */
    public function index(Request $request): View
    {
        $products = Product::all();
        $components = Component::all();
        $prod_comp_list = array();

        foreach ($products as $product) {
            $comps = DB::table('product_component')
                ->select('component_id')
                ->where('product_id', $product->id)
                ->get()->toArray();
            for ($i = 0; $i < count($comps); $i++) {
                $comps[$i] = $comps[$i]->component_id;
            }
            $prod_comp_list[$product->id] = Component::whereIn('id', $comps)->get();
        }
        return view('product.product', [
            'user' => $request->user(),
            'products' => $products,
            'components' => $components,
            'prod_comp_list' => $prod_comp_list,
            'storage_path_components' => 'components',
            'storage_path_products' => 'products'
        ]);

    }

    public function productDetails(Request $request, string $id): View
    {
        $product = Product::find($id);

        $instruction = Instruction::where('product_id', $id)->select('name', 'instruction_pdf', 'video')->get();
        if (count($instruction) > 0) {
            $instruction = $instruction[0];
        }

        $pack_schema_id = StaticValue::where('type','pack_schema')->select('value')->first();
        $pack_schema_id = intval($pack_schema_id->value);
        $schema = ProductionSchema::find($pack_schema_id);
        $prod_prodstd = null;
        if($schema instanceof ProductionSchema and $schema->production_schema == 'Pakowanie produktu') {
            $prod_prodstd = DB::select('select pstd.duration_hours, pstd.amount, u.unit
                                                                from production_standard pstd
                                                                         join unit u
                                                                              on u.id = pstd.unit_id
                                                                where pstd.production_schema_id = '.$schema->id.'
                                                                  and pstd.product_id = '.$id);
            if(is_array($prod_prodstd) and count($prod_prodstd) > 0) {
                $prod_prodstd = $prod_prodstd[0];
            }
        }

        $data = DB::select('select
                                       c.id,
                                       pc.amount_per_product,
                                       c.name,
                                       c.description,
                                       c.independent,
                                       c.material,
                                       c.image,
                                       c.height,
                                       c.length,
                                       c.width
                                from product_component pc
                                join component c
                                    on pc.component_id = c.id
                                where pc.product_id = ' . $id .
            ' order by pc.component_id asc');

        $user = Auth::user();
        if (!is_null($product)) {
            return view('product.product-details', [
                'prod' => $product,
                'data' => $data,
                'instruction' => $instruction,
                'user' => $user,
                'pack_prod_std' => $prod_prodstd,
                'storage_path_products' => 'products',
                'storage_path_instructions' => 'instructions',
                'storage_path_components' => 'components',
            ]);
        }

        return view('product.product-details', [
            'error_msg' => 'Brak danych dla produktu.',
        ]);


    }


    public function addProduct(Request $request, ?string $id = null): View
    {
        $data = $this->getAddProductData(false);

        $status = $request->session()->get('status');
        return view('product.product-add', [
            'components' => $data['components'],
            'comp_data' => $data['comp_prod_schemas'],
            'units' => $data['units'],
            'material_list' => $data['materials'],
            'user' => $request->user(),
            'status' => $status,

        ]);
    }

    public function editProduct(Request $request, string $id): View|RedirectResponse
    {

        if ($id != null) {
            $prod = Product::find($id);
            if ($prod instanceof Product) {

                $pack_schema_id = StaticValue::where('type','pack_schema')->select('value')->first();
                $pack_schema_id = intval($pack_schema_id->value);
                $schema = ProductionSchema::find($pack_schema_id);
                $selected_prod_prodstd = null;
                if($schema instanceof ProductionSchema and $schema->production_schema == 'Pakowanie produktu') {
                    $selected_prod_prodstd = DB::select('select pstd.duration_hours, pstd.amount, u.unit
                                                                from production_standard pstd
                                                                         join unit u
                                                                              on u.id = pstd.unit_id
                                                                where pstd.production_schema_id = '.$schema->id.'
                                                                  and pstd.product_id = '.$prod->id);
                    if(is_array($selected_prod_prodstd) and count($selected_prod_prodstd) > 0) {
                        $selected_prod_prodstd = $selected_prod_prodstd[0];
                    }
                }
                $selected_prod_instr = Instruction::where('product_id', $prod->id)
                    ->select('instruction_pdf', 'video')->get();
                $selected_prod_instr = count($selected_prod_instr) > 0 ? $selected_prod_instr[0] : null;

                $data = $this->getAddProductData(true, $prod->id);
                $selected_prod_comps = DB::select('select
                                            pc.product_id,
                                            c.id as comp_id,
                                            pc.amount_per_product,
                                            c.name,
                                            c.description,
                                            c.independent,
                                            c.material,
                                            c.height,
                                            c.length,
                                            c.width
                                        from component c
                                             join product_component pc
                                                       on c.id = pc.component_id
                                                       and pc.product_id = '.$prod->id.'
                                        order by pc.product_id, c.id');

                $component_input = '';
                $prod_comps = ProductComponent::where('product_id', $id)->select('component_id')->get();
                foreach ($prod_comps as $comp) {
                    $component_input .= $comp->component_id . '_';
                }
                $component_input = substr($component_input, 0, strlen($component_input) - 1);

                $update = str_contains($request->url(), 'edytuj');

                $status = $request->session()->get('status');
                return view('product.product-add', [
                    'components' => $data['components'],
                    'comp_data' => $data['comp_prod_schemas'],
                    'units' => $data['units'],
                    'material_list' => $data['materials'],
                    'user' => $request->user(),
                    'selected_prod' => $prod,
                    'selected_prod_comps' => $selected_prod_comps,
                    'selected_prod_instr' => $selected_prod_instr,
                    'selected_prod_prodstd' => $selected_prod_prodstd,
                    'component_input' => $component_input,
                    'update' => $update,
                    'status' => $status
                ]);
            }
        }
        return redirect()->route('product.index')->with('status_err', 'Nie znaleziono produktu');
    }
    public function storeProduct(Request $request): RedirectResponse
    {
        $this->validateAddProductForm($request, 'INSERT');
        try {
            $this->validateComponentAmount($request);
        }
        catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors(['amount_per_product' => $e->getMessage()]);
        }

        $user = Auth::user();
        $height = doubleval($request->height);
        $length = doubleval($request->length);
        $width = doubleval($request->width);
        $price = floatval($request->price);
        $piecework_fee = floatval($request->piecework_fee);
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        $saved_files = [];
        try {

            DB::beginTransaction();

            $prod_image = !empty($request->file('prod_image')) ? $request->file('prod_image') : $request->prod_image_file_to_copy;
            $barcode_image = !empty($request->file('prod_barcode')) ? $request->file('prod_barcode') : $request->prod_barcode_file_to_copy;
            $insert_result = $this->insertProduct($employee_no, $request->name, $request->gtin, $request->material,
                                                    $request->color, $price, $height, $length, $width, $piecework_fee,
                                                    $request->description, $prod_image, $barcode_image);

            if (array_key_exists('SAVED_FILES', $insert_result)) {
                $saved_files['products'] = $insert_result['SAVED_FILES'];
            }

            if (array_key_exists('ERROR', $insert_result)) {
                throw new Exception('error occurred in ProductController->insertProduct method.
    Error message: ' . $insert_result['ERROR']);
            }

            $prod_id = array_key_exists('ID', $insert_result) ? $insert_result['ID'] : 0;
            if ($prod_id == 0) {
                throw new Exception('error occurred after insert to product table. Failed to evaluate id of inserted product.');

            }

            $insert_result = $this->insertProductComponents($request, $prod_id, $employee_no);
            if (array_key_exists('ERROR', $insert_result)) {
                throw new Exception('error occurred in ProductController->insertProductComponents method.
    Error message: ' . $insert_result['ERROR']);
            }

            if(!in_array(null,array($request->amount,$request->duration, $request->unit))) {
                $amount = floatval($request->amount);
                $duration = floatval($request->duration);
                $this->insertProductProdStd($prod_id, $amount,$duration, $request->unit, $employee_no);
            }

            $instr_pdf = !empty($request->file('instr_pdf')) ? $request->file('instr_pdf') : $request->instr_pdf_file_to_copy;
            $instr_video = !empty($request->file('instr_video')) ? $request->file('instr_video') : $request->instr_video_file_to_copy;

            $instr_name = 'Instrukcja wykonania produktu: '.$request->name;
            $insert_result = InstructionController::insertInstruction($prod_id, 'product_id', $instr_name ,$employee_no, $instr_pdf, $instr_video);

            if (array_key_exists('SAVED_FILES', $insert_result)) {
                $saved_files['instructions'] = $insert_result['SAVED_FILES'];
            }

            if (array_key_exists('ERROR', $insert_result)) {
                throw new Exception('error occurred in InstructionController->insertInstruction method.
    Error message: ' . $insert_result['ERROR']);
            }
            DB::commit();
            //DB::rollBack();

        } catch (Exception $e) {
            Log::channel('error')->error('Error inserting product: ' . $e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            DB::rollBack();

            foreach ($saved_files as $path => $files) {
                if(is_array($files)) {
                    foreach ($files as $file) {
                        fileTrait::deleteFile($path, $file);
                    }
                }
                else if(is_string($files)) {
                    fileTrait::deleteFile($path, $files);
                }
            }

            if (isset($insert_result) and array_key_exists('ERROR', $insert_result)) {
                return back()->with('status', 'Nowy produkt nie został dodany: '.$insert_result['ERROR'])
                    ->withInput();
            }
            return back()->with('status', 'Nowy produkt nie został dodany: błąd przy wprowadzaniu danych do systemu.')
                ->withInput();
        }
        return redirect()->route('product.index')->with('status', 'Produkt został dodany do systemu.');
    }


    public function storeUpdatedProduct(Request $request): RedirectResponse
    {
        $this->validateAddProductForm($request, 'UPDATE');
        try {
            $this->validateComponentAmount($request);
        }
        catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors(['amount_per_product' => $e->getMessage()]);
        }

        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';

        if (!isset($request->product_id) or empty($request->product_id)) {
            Log::channel('error')->error('Error updating product: error occurred in Product->storeUpdatedProduct method. ID of the component not found', [
                'employeeNo' => $employee_no,
            ]);
            return back()->with('status', 'Nie udało się etytować produktu - nie znaleziono ID.')->withInput();
        }
        if (!(Product::find($request->product_id) instanceof Product)) {
            Log::channel('error')->error('Error updating product: error occurred in Product->storeUpdatedProduct method. Product with id ' . $request->product_id . ' not found', [
                'employeeNo' => $employee_no,
            ]);
            return back()->with('status', 'Nie udało się etytować produktu - nie znaleziono produktu o podanym ID.')->withInput();
        }

        $prod_id = $request->product_id;
        $height = doubleval($request->height);
        $length = doubleval($request->length);
        $width = doubleval($request->width);
        $price = floatval($request->price);
        $piecework_fee = floatval($request->piecework_fee);
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        $saved_files = [];

        try {

            DB::beginTransaction();

            $prod_image = !empty($request->file('prod_image')) ? $request->file('prod_image') : $request->prod_image_file_to_copy;
            $barcode_image = !empty($request->file('prod_barcode')) ? $request->file('prod_barcode') : $request->prod_barcode_file_to_copy;
            $update_result = $this->updateProduct($prod_id, $employee_no, $request->name, $request->gtin, $request->material,
                                                  $request->color, $price, $height, $length, $width,
                                                  $piecework_fee, $request->description, $prod_image, $barcode_image);

            if (array_key_exists('SAVED_FILES', $update_result)) {
                $saved_files['products'] = $update_result['SAVED_FILES'];
            }

            if (array_key_exists('ERROR', $update_result)) {
                throw new Exception('Error updating product: error occurred in Product->updateProduct method.
    Error message: ' . $update_result['ERROR']);
            }

            $update_result = $this->updateProductComponents($request, $prod_id, $employee_no);
            if (array_key_exists('ERROR', $update_result)) {
                throw new Exception('Error updating product: error occurred in Product->updateProductComponents method.
    Error message: ' . $update_result['ERROR']);
            }

            $this->updateProductProdStd($prod_id, $request->amount,$request->duration, $request->unit, $employee_no);


            $instr_pdf = !empty($request->file('instr_pdf')) ? $request->file('instr_pdf') : $request->instr_pdf_file_to_copy;
            $instr_video = !empty($request->file('instr_video')) ? $request->file('instr_video') : $request->instr_video_file_to_copy;

            $instr_name = 'Instrukcja wykonania produktu: '.$request->name;
            $update_result = InstructionController::updateInstruction($prod_id, 'product_id', $instr_name, $employee_no, $instr_pdf, $instr_video);
            if (array_key_exists('SAVED_FILES', $update_result)) {
                    $saved_files['instructions'] = $update_result['SAVED_FILES'];
            }

            if (array_key_exists('ERROR', $update_result)) {
                throw new Exception('Error updating component: error occurred in Component->insertInstruction method.
    Error message: ' . $update_result['ERROR']);
            }
            DB::commit();
            //DB::rollBack();

        } catch (Exception $e) {
            Log::channel('error')->error('Error updating product: ' . $e->getMessage(), [
                'employeeNo' => $employee_no,
            ]);
            DB::rollBack();

            foreach ($saved_files as $path => $files) {
                if(is_array($files)) {
                    foreach ($files as $file) {
                        fileTrait::deleteFile($path, $file);
                    }
                }
                else if(is_string($files)) {
                    fileTrait::deleteFile($path, $files);
                }
            }

            if (isset($update_result) and array_key_exists('ERROR', $update_result)) {
                return back()->with('status', $update_result['ERROR'])
                    ->withInput();
            }
            return back()->with('status', 'Produkt nie został edytowany: błąd przy wprowadzaniu danych do systemu.')
                ->withInput();
        }
        return redirect()->route('product.index')->with('status', 'Edytowano produkt.');
    }


    public function destroyProduct(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'confirmation' => ['regex:(usuń|usun)'],
            ],
                [
                    'confirmation.regex' => 'Nie można usunąć produktu: niepoprawna wartość. Wpisz "usuń".',
                ]);
        }
        catch (Exception $e) {
            return redirect()->back()->with('status_err', $e->getMessage());
        }


        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';
        $prod_id = $request->remove_id;
        $prod = Product::find($prod_id);
        if($prod instanceof Product) {
            try {

                DB::beginTransaction();

                ProductComponent::where('product_id', $prod_id)->delete();

                $instr= Instruction::where('product_id',$prod_id)
                    ->select('instruction_pdf','video')
                    ->get();
                $instr = count($instr) == 1 ? $instr[0] : null;

                Instruction::where('product_id', $prod_id)->delete();
                Product::where('id', $prod_id)->delete();

                if($instr instanceof Instruction) {
                    if(fileTrait::fileExists('instructions', $instr->instruction_pdf)) {
                        fileTrait::deleteFile('instructions', $instr->instruction_pdf);
                    }
                    if(fileTrait::fileExists('instructions', $instr->video)) {
                        fileTrait::deleteFile('instructions', $instr->video);
                    }
                }
                if(fileTrait::fileExists('products', $prod->image)) {
                    fileTrait::deleteFile('products', $prod->image);
                }
                if(fileTrait::fileExists('products', $prod->barcode_image)) {
                    fileTrait::deleteFile('products', $prod->barcode_image);
                }

                DB::commit();

            } catch (Exception $e) {
                Log::channel('error')->error('Error deleting product: ' . $e->getMessage(), [
                    'employeeNo' => $employee_no,
                ]);
                DB::rollBack();

                return back()->with('status_err', 'Produkt nie został usunięty: błąd przy usuwaniu danych z systemu.')
                    ->withInput();
            }
        }

        return  redirect()->route('product.index')
            ->with('status', 'Usunięto produkt: '.$prod->name.'.')
            ->withInput();
    }


    /**
     * @throws Exception
     */
    private function validateComponentAmount(Request $request): void
    {
        $user = Auth::user();
        $employee_no = !empty($user->employeeNo) ? $user->employeeNo : 'unknown';

        if(!empty($request->component_input)) {
            $comps = explode('_',$request->component_input);
            for ($i = 0; $i < count($comps); $i++) {
                $amount_name = 'amount_'.$comps[$i];
                if(intval($request->$amount_name) <= 0) {
                    throw new Exception('Ilość sztuk potrzebnych do wykonania produktu musi być dodatnia.');
                }
                $comp = intval($comps[$i]);
                if($comp <= 0) {
                    Log::channel('error')->error('Error validating product: Error occurred in Product->validateComponentAmount method. Incorrect "component_input" value:' . $request->component_input, [
                        'employeeNo' => $employee_no,
                    ]);
                    throw new Exception('Produkt nie został dodany. Błąd systemu.');
                }
                $comps[$i] = $comp;
            }
        }
    }
    private function validateAddProductForm(Request $request, string $action) : void
    {

        $materials = StaticValue::where('type','material')->select('value', 'value_full')->get();
        $units = Unit::all();

        $unit_err_mess = '';
        $unit_in = 'in:';
        foreach ($units as $unit) {
            $unit_in .= $unit->unit.',';
            $unit_err_mess .= $unit->unit.' ,';
        }

        $unit_in = rtrim($unit_in,',');
        $unit_err_mess = rtrim($unit_err_mess,',');

        $err_mess = '';
        $mat_in = 'in:';
        foreach ($materials as $mat) {
            $mat_in .= $mat->value.',';
            $err_mess .= $mat->value_full.' ,';
        }
        $mat_in = rtrim($mat_in,',');
        $err_mess = rtrim($err_mess,',');

        $ext_prod_image = empty($request->file('prod_image')) ? '' : $request->file('prod_image')->extension();
        $ext_prod_barcode = empty($request->file('prod_barcode')) ? '' : $request->file('prod_barcode')->extension();
        $ext_instr_pdf = empty($request->file('instr_pdf')) ? '' : $request->file('instr_pdf')->extension();
        $ext_instr_video = empty($request->file('instr_video')) ? '' : $request->file('instr_video')->extension();

        $name_rules = ['required', 'string',  'min:1','max:100'];
        if($action == 'INSERT') {
            $name_rules[] =  'unique:'.Product::class;
        }
        $request->validate([
            'name' => $name_rules,
            'gtin' => ['nullable','between:12,14'],
            'material' => ['nullable', $mat_in],
            'prod_image' => ['mimes:jpeg,gif,bmp,png,jpg,svg', 'max:16384'],
            'prod_barcode' => ['mimes:jpeg,gif,bmp,png,jpg,svg,pdf', 'max:16384'],
            'instr_pdf' => ['mimes:pdf', 'max:16384'],
            'instr_video' => ['mimes:mp4,mov,mkv,wmv', 'max:51300'],
            'height' => ['gte:0'],
            'length' => ['gte:0'],
            'width' => ['gte:0'],
            'color' => ['max:30'],
            'price' => ['gte:0'],
            'piecework_fee' => ['gte:0'],
            'description' => ['max:255'],
            'unit' => ['nullable', $unit_in],
            'amount' => ['nullable', 'gt:0'],
            'duration' => ['nullable', 'integer', 'gt:0'],
        ],
            [
                'name.unique' => 'Nazwa materiału musi być unikalna.',
                'gtin.between' => 'GTIN powinien mieć długość od 12 do 14 cyfr',
                'material.in' => 'Wybierz jeden z surowców: '.$err_mess.'.',
                'prod_image.mimes' => 'Przesłany plik powinien mieć rozszerzenie: jpeg,bmp,png,jpg,svg. Rozszerzenie pliku: '.$ext_prod_image.'.',
                'prod_barcode_mimes' => 'Przesłany plik powinien mieć rozszerzenie: jpeg,gif,bmp,png,jpg,svg,pdf. Rozszerzenie pliku: '.$ext_prod_barcode.'.',
                'instr_pdf.mimes' => 'Przesłany plik powinien mieć rozszerzenie: pdf. Rozszerzenie pliku: '.$ext_instr_pdf.'.',
                'instr_video.mimes' => 'Przesłany plik powinien mieć rozszerzenie: mp4,mov,mkv,wmv. Rozszerzenie pliku: '.$ext_instr_video.'.',
                'prod_image.max' => 'Przesłany plik jest za duży. Maksymalny rozmiar pliku: 16 MB.',
                'instr_pdf.max' => 'Przesłany plik jest za duży. Maksymalny rozmiar pliku: 16 MB.',
                'instr_video.max' => 'Przesłany plik jest za duży. Maksymalny rozmiar pliku: 50 MB.',
                'price.gte' => 'Cena musi być liczbą nie mniejszą niż 0.',
                'piecework_fee' => 'Stawka musi być liczbą nie mniejszą niż 0.',
                'required' => 'To pole jest wymagane.',
                'height.gte' => 'Wysokość nie może być ujemna.',
                'length.gte' => 'Długość nie może być ujemna.',
                'width.gte' => 'Szerokość nie może być ujemna.',
                'max' => 'Wpisany tekst ma za dużo znaków.',
                'min' => 'Wpisany tekst ma za mało znaków.',
                'unit.in' => 'Wybierz jedną z jednostek: '.$unit_err_mess.'.',
                'amount.gt' => 'Ilość musi być większa od 0',
                'duration.gt' => 'Czas trwania musi być większy od 0',
                'duration.integer' => 'Czas trwania musi być liczbą całkowitą'
            ]);


    }

    private function insertProduct(string $employee_no, string $name, string|null $gtin, string|null $material, string|null $color,
                                   float|null $price, float|null $height, float|null $length, float|null $width,
                                   float|null $piecework_fee, string|null $description, $prod_image, $barcode_image ): array
    {

        $prod_id = DB::table('product')->insertGetId([
            'name' => $name,
            'gtin' =>$gtin,
            'description' => $description,
            'material' => $material,
            'height' => $height,
            'length' => $length,
            'width' => $width,
            'color' => $color,
            'image' => '',
            'barcode_image' => '',
            'price' => $price,
            'piecework_fee' => $piecework_fee,
            'created_by' => $employee_no,
            'updated_by' => $employee_no,
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        $image_name = '';
        if($prod_image instanceof UploadedFile) {
            $image_name = fileTrait::saveFile($prod_image, 'products', 'prod_'.$prod_id.'_');
            //if failed to save prod image file
            if(empty($image_name)) {
                return array('ERROR' => 'błąd przy zapisie pliku "Zdjęcie produktu" na dysku.');
            }
        }
        else if(is_string($prod_image)) {
            $new_image_name = fileTrait::getFileName('products', $prod_image);
            if(!fileTrait::copyFile('products', $prod_image, 'products', $new_image_name)) {
                return array('ERROR' => 'błąd przy kopiowaniu pliku "Zdjęcie produktu" na dysku.');
            }
            else {
                $image_name = $new_image_name;
            }
        }

        $barcode_image_name = '';
        if($barcode_image instanceof UploadedFile) {
            $barcode_image_name = fileTrait::saveFile($barcode_image, 'products', 'prod_brcd_'.$prod_id.'_');
            //if failed to save comp instr video file
            if(empty($barcode_image_name)) {
                return array('ERROR' => 'błąd przy zapisie pliku "Kod kreskowy".',
                    'SAVED_FILES' => [$image_name]);
            }
        }
        else if(is_string($barcode_image)) {
            $new_barcode_image_name = fileTrait::getFileName('products', $barcode_image);
            if(!fileTrait::copyFile('products', $barcode_image, 'products', $new_barcode_image_name)) {
                return array('ERROR' => 'błąd przy kopiowaniu pliku "Kod kreskowy".',
                    'SAVED_FILES' => [$image_name]);
            }
            else {
                $barcode_image_name = $new_barcode_image_name;
            }
        }

        $saved_files = [];
        if(!empty($image_name)) {
            $saved_files[] = $image_name;
        }
        if(!empty($barcode_image_name)) {
            $saved_files[] = $barcode_image_name;
        }

        if(count($saved_files) > 0) {
            try {
                DB::table('product')
                    ->where('id', $prod_id)
                    ->update(['image' => $image_name,
                              'barcode_image' => $barcode_image_name]);

            } catch(Exception $e) {
                Log::channel('error')->error('Error inserting product: '.$e->getMessage(), [
                    'employeeNo' => $employee_no,
                ]);
                return array('ERROR' => 'błąd przy zapisie nazwy pliku "Zdjęcie produktu" w bazie danych.',
                    'SAVED_FILES' => $saved_files);
            }
        }
        return array('SAVED_FILES' => $saved_files,
            'ID' => $prod_id);
    }


    private function updateProduct(int $prod_id, string $employee_no, string $name, string|null $gtin, string|null $material,
                                   string|null $color, float|null $price, float|null $height, float|null $length, float|null $width,
                                   float|null $piecework_fee, string|null $description, $prod_image, $barcode_image ): array
    {
        $prod_old = Product::find($prod_id);
        if($prod_old instanceof Product) {
            DB::table('product')
                ->where('id', $prod_id)
                ->update([
                    'name' => $name,
                    'gtin' =>$gtin,
                    'description' => $description,
                    'material' => $material,
                    'height' => $height,
                    'length' => $length,
                    'width' => $width,
                    'color' => $color,
                    'image' => '',
                    'barcode_image' => '',
                    'price' => $price,
                    'piecework_fee' => $piecework_fee,
                    'updated_by' => $employee_no,
                    'updated_at' => date('y-m-d h:i:s'),
                ]);


            $image_name = '';
            if($prod_image instanceof UploadedFile) {
                $image_name = fileTrait::saveFile($prod_image, 'products', 'prod_'.$prod_id.'_');
                //if failed to save prod image file
                if(empty($image_name)) {
                    return array('ERROR' => 'Produkt nie został edytowany: błąd przy zapisie pliku "Zdjęcie produktu" na dysku.');
                }
                if(!empty($prod_old->image) and fileTrait::fileExists('products', $prod_old->image)) {
                    fileTrait::deleteFile('products', $prod_old->image);
                }

            }
            else if(is_null($prod_image)) {
                if(!empty($prod_old->image) and fileTrait::fileExists('products', $prod_old->image)) {
                    fileTrait::deleteFile('products', $prod_old->image);
                }
                $image_name = null;
            }

            $barcode_image_name = '';
            if($barcode_image instanceof UploadedFile) {
                $barcode_image_name = fileTrait::saveFile($barcode_image, 'products', 'prod_brcd_'.$prod_id.'_');
                //if failed to save comp instr video file
                if(empty($barcode_image_name)) {
                    if(empty($image_name)){
                        return array('ERROR' => 'błąd przy zapisie pliku "Kod kreskowy" na dysku.');
                    }
                    return array('ERROR' => 'błąd przy zapisie pliku "Kod kreskowy".',
                        'SAVED_FILES' => array($image_name));
                }
                if(!empty($prod_old->barcode_image) and fileTrait::fileExists('products', $prod_old->barcode_image)) {
                    fileTrait::deleteFile('products', $prod_old->barcode_image);
                }
            }
            else if(is_null($barcode_image)) {
                if(!empty($prod_old->barcode_image) and fileTrait::fileExists('products', $prod_old->barcode_image)) {
                    fileTrait::deleteFile('products', $prod_old->barcode_image);
                }
                $barcode_image_name = null;
            }

            $saved_files = [];
            if(!empty($image_name)) {
                $saved_files[] = $image_name;
            }
            else if(!is_null($image_name)) {
                $image_name = $prod_old->image;
            }
            if(!empty($barcode_image_name)) {
                $saved_files[] = $barcode_image_name;
            }
            else if(!is_null($barcode_image_name)) {
                $barcode_image_name = $prod_old->barcode_image;
            }

            try {
                DB::table('product')
                    ->where('id', $prod_id)
                    ->update(['image' => $image_name,
                        'barcode_image' => $barcode_image_name]);

            } catch(Exception $e) {
                Log::channel('error')->error('Error updating product: '.$e->getMessage(), [
                    'employeeNo' => $employee_no,
                ]);
                return array('ERROR' => 'Produkt nie został edytowany: błąd przy zapisie nazwy pliku "Zdjęcie produktu" lub "Kod kreskowy" w bazie danych.',
                    'SAVED_FILES' => $saved_files);
            }
            return array('SAVED_FILES' => $saved_files);
        }
        Log::channel('error')->error('Error updating product: Product with id '.$prod_id.' not found', [
            'employeeNo' => $employee_no,
        ]);
        return array('ERROR' => 'Produkt nie został edytowany: nie znaleziono wybranego produktu.');
    }


    private function insertProductComponents(Request $request, int $prod_id, string $employee_no): array
    {
        if(is_null($request->component_input)) {
            return [];
        }

        $comps = explode('_',$request->component_input);

        foreach ($comps as $comp) {
            $amount_name = 'amount_'.$comp;
            $amount_per_product = $request->$amount_name;
            $comp_id = intval($comp);
            if(Component::find($comp_id) instanceof Component) {

                DB::table('product_component')->insert([
                    'product_id' => $prod_id,
                    'component_id' => $comp_id,
                    'amount_per_product' => $amount_per_product,
                    'created_by' => $employee_no,
                    'updated_by' => $employee_no,
                    'created_at' => date('y-m-d h:i:s'),
                    'updated_at' => date('y-m-d h:i:s'),
                ]);
            }
            else {
                Log::channel('error')->error('Error inserting product_component: Component not found for value ' .$request->component_input. ' of "component_input" input.', [
                    'employeeNo' => $employee_no,
                ]);
                return ['ERROR' => 'Nie znaleziono materiału dla wybranych materiałów.'];
            }
        }
        return [];
    }
    private function updateProductComponents(Request $request, int $prod_id, string $employee_no): array
    {
        $old_comps_id = ProductComponent::where(['product_id' => $prod_id])
            ->select('component_id')->get();
        $old_comps_id = collect($old_comps_id)->map(function (ProductComponent $arr) { return $arr->component_id; })->toArray();
        if(!is_null($request->component_input)) {
            $comps = explode('_',$request->component_input);

            foreach ($comps as $comp) {
                $comp_id = intval($comp);
                $amount_name = 'amount_'.$comp;
                $amount_per_product = $request->$amount_name;
                if(in_array($comp_id, $old_comps_id)) {
                    $prod_comp = ProductComponent::where(['component_id' => $comp_id, 'product_id' => $prod_id])
                                ->select('amount_per_product')->first();
                    if($prod_comp instanceof  ProductComponent and $prod_comp->amount_per_product != $amount_per_product) {
                        DB::table('product_component')
                            ->where(['product_id' => $prod_id,
                                     'component_id' => $comp_id,])
                            ->update([
                            'amount_per_product' => $amount_per_product,
                            'updated_by' => $employee_no,
                            'updated_at' => date('y-m-d h:i:s'),
                        ]);
                    }
                    array_splice($old_comps_id,array_search($comp_id, $old_comps_id),1);
                }
                else if(Component::find($comp_id) instanceof Component) {
                    DB::table('product_component')->insert([
                        'product_id' => $prod_id,
                        'component_id' => $comp_id,
                        'amount_per_product' => $amount_per_product,
                        'created_by' => $employee_no,
                        'updated_by' => $employee_no,
                        'created_at' => date('y-m-d h:i:s'),
                        'updated_at' => date('y-m-d h:i:s'),
                    ]);
                }
                else {
                    Log::channel('error')->error('Error inserting product_component: Component not found for value ' .$request->component_input. ' of "component_input" input.', [
                        'employeeNo' => $employee_no,
                    ]);
                    return ['ERROR' => 'nie znaleziono materiału dla wybranych materiałów.'];
                }
            }
        }

        foreach ($old_comps_id as $old_comp) {
            $old_comp_id = intval($old_comp);

            DB::table('product_component')
                ->where(['product_id' => $prod_id,
                        'component_id' => $old_comp_id])
                ->delete();
        }
        return [];
    }

    private function getAddProductData(bool $adjusted_to_component, int $product_id = 0): array
    {
        $materials = StaticValue::where('type','material')->get();
        $units = Unit::select('unit','name')->get();
        $components = Component::all();
        if($adjusted_to_component) {
            $data = DB::select('select
                                            c.id as comp_id,
                                            pc.amount_per_product,
                                            c.name,
                                            c.description,
                                            c.independent,
                                            c.material,
                                            c.height,
                                            c.length,
                                            c.width,
                                            psh.id as prod_schema_id,
                                            psh.production_schema as prod_schema,
                                            cpsh.sequence_no as prod_schema_sequence_no,
                                            psh.description as prod_schema_desc,
                                            psh.tasks_count,
                                            pstd.duration_hours as prod_std_duration,
                                            pstd.amount as prod_std_amount,
                                            u.unit as prod_std_unit
                                        from component c
                                             left join component_production_schema cpsh
                                                       on cpsh.component_id = c.id
                                             left join production_schema psh
                                                       on cpsh.production_schema_id = psh.id
                                             left join production_standard pstd
                                                       on pstd.component_id = c.id
                                                           and pstd.production_schema_id = psh.id
                                             left join unit u
                                                       on u.id = pstd.unit_id
                                             left join product_component pc
                                                       on c.id = pc.component_id
                                        order by pc.product_id, c.id, cpsh.sequence_no');
        } else {
            $data = DB::select('select
                                            c.id as comp_id,
                                            pc.amount_per_product,
                                            c.name,
                                            c.description,
                                            c.independent,
                                            c.material,
                                            c.height,
                                            c.length,
                                            c.width,
                                            psh.id as prod_schema_id,
                                            psh.production_schema as prod_schema,
                                            cpsh.sequence_no as prod_schema_sequence_no,
                                            psh.description as prod_schema_desc,
                                            psh.tasks_count,
                                            pstd.duration_hours as prod_std_duration,
                                            pstd.amount as prod_std_amount,
                                            u.unit as prod_std_unit
                                        from component c
                                             left join component_production_schema cpsh
                                                       on cpsh.component_id = c.id
                                             left join production_schema psh
                                                       on cpsh.production_schema_id = psh.id
                                             left join production_standard pstd
                                                       on pstd.component_id = c.id
                                                           and pstd.production_schema_id = psh.id
                                             left join unit u
                                                       on u.id = pstd.unit_id
                                             left join product_component pc
                                                       on c.id = pc.component_id
                                        order by c.id, cpsh.sequence_no');
        }


        $comp_prod_schemas = array();
        if(count($data) > 0) {
            $curr_id = $data[0]->comp_id;
            $temp = [];

            foreach ($data as $row) {
                if ($row->comp_id != $curr_id) {
                    $comp_prod_schemas[$curr_id] = $temp;
                    $curr_id = $row->comp_id;
                    $temp = [];
                }
                $temp[] = $row;
            }
            $comp_prod_schemas[$curr_id] = $temp;
        }

        return array('materials' => $materials,
            'units' => $units,
            'comp_prod_schemas' => $comp_prod_schemas,
            'components' => $components
        );
    }

    /**
     * @throws Exception
     */
    private function insertProductProdStd(int $prod_id, float $amount, float $duration, string $unit, string $employee_no ): void
    {
        $pack_schema_id = StaticValue::where('type','pack_schema')->select('value')->first();
        $pack_schema_id = intval($pack_schema_id->value);
        $schema = ProductionSchema::find($pack_schema_id);
        if(!($schema instanceof ProductionSchema and $schema->production_schema == 'Pakowanie produktu')) {
            throw new Exception('Error inserting product: error occurred in Product->insertProductProdStd method. Pack schema not found with id '.$pack_schema_id.'.');
        }
        $unit_id = Unit::where('unit',$unit)->select('id')->first();
        if($unit_id instanceof Unit) {
            $unit_id = $unit_id->id;
        }
        else {
            throw new Exception('Error inserting product: error occurred in Product->insertProductProdStd method. Id not found for provided unit.');
        }
        DB::table('production_standard')->insert([
            'production_schema_id' => $pack_schema_id,
            'product_id' => $prod_id,
            'name' => '',
            'duration_hours' => $duration,
            'amount' =>$amount,
            'unit_id' => $unit_id,
            'created_by' => $employee_no,
            'updated_by' => $employee_no,
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }

    /**
     * @throws Exception
     */
    private function updateProductProdStd(int $prod_id, float|null $amount,float|null $duration,string|null $unit, $employee_no): void
    {
        $pack_schema_id = StaticValue::where('type','pack_schema')->select('value')->first();
        $pack_schema_id = intval($pack_schema_id->value);
        $schema = ProductionSchema::find($pack_schema_id);
        if(!($schema instanceof ProductionSchema and $schema->production_schema == 'Pakowanie produktu')) {
            throw new Exception('Error updating product: error occurred in Product->updateProductProdStd method. Pack schema not found with id '.$pack_schema_id.'.');
        }
        $unit_id = Unit::where('unit',$unit)->select('id')->first();
        if($unit_id instanceof Unit) {
            $unit_id = $unit_id->id;
        }
        else {
            throw new Exception('Error updating product: error occurred in Product->updateProductProdStd method. Id not found for provided unit.');
        }
        if(is_null($duration) and is_null($amount)) {
            if(ProductionStandard::where(['product_id'=> $prod_id,
                                          'production_schema_id' => $pack_schema_id])->first() instanceof ProductionStandard) {
                ProductionStandard::where(['product_id'=> $prod_id,
                                           'production_schema_id' => $pack_schema_id])->delete();
            }
        } else {
            $duration = floatval($duration);
            $amount = floatval($amount);
            if(ProductionStandard::where(['product_id'=> $prod_id,
                                          'production_schema_id' => $pack_schema_id])->first() instanceof ProductionStandard) {
                DB::table('production_standard')
                    ->where(['product_id'=> $prod_id,
                             'production_schema_id' => $pack_schema_id])
                    ->update([
                        'duration_hours' => $duration,
                        'amount' =>$amount,
                        'unit_id' => $unit_id,
                        'updated_by' => $employee_no,
                        'updated_at' => date('y-m-d h:i:s'),
                    ]);
            } else {
                DB::table('production_standard')->insert([
                    'production_schema_id' => $pack_schema_id,
                    'product_id'=> $prod_id,
                    'duration_hours' => $duration,
                    'amount' =>$amount,
                    'unit_id' => $unit_id,
                    'created_by' => $employee_no,
                    'updated_by' => $employee_no,
                    'created_at' => date('y-m-d h:i:s'),
                    'updated_at' => date('y-m-d h:i:s'),
                ]);
            }
        }
    }
}
