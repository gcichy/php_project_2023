@if(isset($placeholder) and isset($options) and isset($uniqueId))
    <script>
        function dropdown() {
            return {
                options: [],
                selected: [],
                show: false,
                open() { this.show = true },
                close() { this.show = false },
                isOpen() { return this.show === true },
                select(index, event, onStart) {

                    if (!this.options[index].selected || onStart) {
                        console.log(event);
                        this.options[index].selected = true;
                        this.options[index].element = event.target;
                        this.selected.push(index);

                    } else {
                        this.selected.splice(this.selected.lastIndexOf(index), 1);
                        this.options[index].selected = false
                    }
                },
                remove(index, option) {
                    this.options[option].selected = false;
                    this.selected.splice(index, 1);


                },
                loadOptions(id) {
                    let options = document.getElementById(id).options;
                    for (let i = 0; i < options.length; i++) {
                        this.options.push({
                            value: options[i].value,
                            text: options[i].innerText,
                            selected: options[i].getAttribute('selected') != null
                        });
                    }


                },
                selectedValues(){
                    return this.selected.map((option)=>{
                        return this.options[option].value;
                    })
                },
                checkAndSelectOption(option, index) {
                    if (option.selected) {
                        if (this.$el.classList.contains('item-to-select')) {
                            this.select(index, { target: this.$el },true);
                        }
                    }
                },
            }
        }
    </script>
<style>
    [x-cloak] {
        display: none;
    }
</style>
    <select x-cloak id="select-{{$uniqueId}}">
        {{$options}}
    </select>

    <div x-data="dropdown()" x-init="loadOptions('select-{{$uniqueId}}')" class="w-full flex flex-col items-center mx-auto">
        <input name="{{$uniqueId}}" id="{{$uniqueId}}-input" type="text" class="hidden" x-bind:value="selectedValues()">
        <div class="inline-block relative w-full">
            <div class="flex flex-col items-center relative">
                <div x-on:click="open" class="w-full  svelte-1l8159u">
                    <div class="my-2 xl:p-1 flex border border-gray-200 bg-white rounded text-xs xl:text-sm svelte-1l8159u">
                        <div x-show="selected.length    == 0" class="flex-1">
                            <input placeholder="{{$placeholder}}"
                                   class="bg-transparent p-1 px-0.5 appearance-none outline-none h-full w-full text-gray-800"
                                   x-bind:value="selectedValues()"
                            >
                        </div>
                        <div class="flex flex-auto overflow-hidden">
                            <template x-for="(option,index) in selected" :key="options[option].value">
                                <div
                                    class="flex justify-center items-center m-1 font-medium px-2 rounded-full text-blue-800 bg-blue-150 border border-blue-800 ">
                                    <div class="text-xs font-normal leading-none max-w-full flex-initial x-model="
                                         options[option]" x-text="options[option].text"></div>
                                <div class="flex flex-auto flex-row-reverse">
                                    <div x-on:click="remove(index,option)">
                                        <svg class="fill-current h-6 w-6 " role="button" viewBox="0 0 20 20">
                                            <path d="M14.348,14.849c-0.469,0.469-1.229,0.469-1.697,0L10,11.819l-2.651,3.029c-0.469,0.469-1.229,0.469-1.697,0
                                       c-0.469-0.469-0.469-1.229,0-1.697l2.758-3.15L5.651,6.849c-0.469-0.469-0.469-1.228,0-1.697s1.228-0.469,1.697,0L10,8.183
                                       l2.651-3.031c0.469-0.469,1.228-0.469,1.697,0s0.469,1.229,0,1.697l-2.758,3.152l2.758,3.15
                                       C14.817,13.62,14.817,14.38,14.348,14.849z" />
                                        </svg>

                                    </div>
                                </div>
                            </template>
                        </div>
                        <div
                            class="text-gray-300 w-8 py-1 pl-2 pr-1 border-l flex items-center border-gray-200 svelte-1l8159u">

                            <button type="button" x-show="isOpen() === true" x-on:click="open"
                                    class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none">
                                <svg version="1.1" class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                    <path d="M17.418,6.109c0.272-0.268,0.709-0.268,0.979,0s0.271,0.701,0,0.969l-7.908,7.83
c-0.27,0.268-0.707,0.268-0.979,0l-7.908-7.83c-0.27-0.268-0.27-0.701,0-0.969c0.271-0.268,0.709-0.268,0.979,0L10,13.25
L17.418,6.109z" />
                                </svg>

                            </button>
                            <button type="button" x-show="isOpen() === false" @click="close"
                                    class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none">
                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                    <path d="M2.582,13.891c-0.272,0.268-0.709,0.268-0.979,0s-0.271-0.701,0-0.969l7.908-7.83
c0.27-0.268,0.707-0.268,0.979,0l7.908,7.83c0.27,0.268,0.27,0.701,0,0.969c-0.271,0.268-0.709,0.268-0.978,0L10,6.75L2.582,13.891z
" />
                                </svg>

                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full px-4">
                <div x-show.transition.origin.top="isOpen()"
                     class="absolute shadow top-100 bg-white z-40 w-full left-0 rounded max-h-select overflow-y-auto"
                     x-on:click.away="close">
                    <div class="flex flex-col w-full">
                        <template x-for="(option,index) in options" :key="option">
                            <div>
                                <div x-bind:class="{ 'item-to-select' : option.selected }" class="cursor-pointer w-full border-gray-100 rounded-t border-b hover:bg-blue-150"
                                     @click="select(index,$event,false)" x-init="checkAndSelectOption(option, index)">
{{--                                    if option.selected (from the line below) is true I want to trigger @click="select(index,$event) event from the line above--}}
                                    <div x-bind:class="option.selected ? 'border-blue-450' : ''"
                                         class="flex w-full items-center p-2 pl-2 border-l-4 relative">
                                        <div class="w-full items-center flex text-xs xl:text-sm">
                                            <div class="mx-2 leading-6" x-model="option" x-text="option.text"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
