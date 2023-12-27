<div class="mt-6 flex flex-col xl:flex-row border-gray-300 xl:bg-white justify-between items-center z-[50]"
     data-te-sticky-init
     data-te-sticky-offset="61"
     data-te-sticky-delay="90"
     data-te-sticky-direction="both">
    <a class ='block w-full xl:w-1/3 py-2 pl-3 pr-4 bg-white border-blue-450 border-l-4 lg:border-l-8 lg:py-6 text-md md:text-lg lg:text-2xl text-left font-medium text-gray-800  transition duration-150 ease-in-out'>
        {{$viewName}}
    </a>
    <div class="p-2 xl:mt-0 w-full xl:w-3/4 bg-gray-50 xl:bg-white flex justify-center xl:justify-end align-middle">
        {{$slot}}
    </div>
</div>

