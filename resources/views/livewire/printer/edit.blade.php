<div>
    <div class="mb-4">
        <h1 class="text-2xl font-semibold dark:text-white">Edit Printer: {{ $title }}</h1>
    </div>

    <form wire:submit.prevent="update">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 flex flex-col my-2 dark:bg-zinc-900 dark:border dark:border-zinc-700">
            <div class="-mx-3 md:flex mb-6">
                <div class="md:w-full px-3">
                    <label class="block uppercase tracking-wide text-grey-darker text-xs font-bold mb-2 dark:text-gray-300" for="title">
                        Title
                    </label>
                    <input wire:model.blur="title" id="title" type="text" placeholder="My Office Printer" class="appearance-none block w-full bg-grey-lighter text-grey-darker border @error('title') border-red-500 @else border-grey-lighter @enderror rounded py-3 px-4 mb-3 dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-300">
                    @error('title') <p class="text-red-500 text-xs italic dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="-mx-3 md:flex mb-6">
                <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-grey-darker text-xs font-bold mb-2 dark:text-gray-300" for="type">
                        Type
                    </label>
                    <div class="relative">
                        <select wire:model.live="type" id="type" class="block appearance-none w-full bg-grey-lighter border border-grey-lighter text-grey-darker py-3 px-4 pr-8 rounded dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-300">
                            <option value="network">Network</option>
                            <option value="usb">USB</option>
                            <option value="serial">Serial</option>
                            <option value="windows">Windows Shared</option>
                            <option value="test">Test/Dummy</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-grey-darker dark:text-gray-400">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
                    </div>
                    @error('type') <p class="text-red-500 text-xs italic dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div class="md:w-1/2 px-3">
                    <label class="block uppercase tracking-wide text-grey-darker text-xs font-bold mb-2 dark:text-gray-300" for="characters_per_line">
                        Characters Per Line (CPL)
                    </label>
                    <input wire:model.blur="characters_per_line" id="characters_per_line" type="number" placeholder="42" class="appearance-none block w-full bg-grey-lighter text-grey-darker border @error('characters_per_line') border-red-500 @else border-grey-lighter @enderror rounded py-3 px-4 mb-3 dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-300">
                    @error('characters_per_line') <p class="text-red-500 text-xs italic dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            @if ($type === 'network')
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-2/3 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-grey-darker text-xs font-bold mb-2 dark:text-gray-300" for="ip_address">
                            IP Address
                        </label>
                        <input wire:model.blur="ip_address" id="ip_address" type="text" placeholder="192.168.1.100" class="appearance-none block w-full bg-grey-lighter text-grey-darker border @error('ip_address') border-red-500 @else border-grey-lighter @enderror rounded py-3 px-4 mb-3 dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-300">
                        @error('ip_address') <p class="text-red-500 text-xs italic dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:w-1/3 px-3">
                        <label class="block uppercase tracking-wide text-grey-darker text-xs font-bold mb-2 dark:text-gray-300" for="port">
                            Port
                        </label>
                        <input wire:model.blur="port" id="port" type="number" placeholder="9100" class="appearance-none block w-full bg-grey-lighter text-grey-darker border @error('port') border-red-500 @else border-grey-lighter @enderror rounded py-3 px-4 mb-3 dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-300">
                        @error('port') <p class="text-red-500 text-xs italic dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            @elseif (in_array($type, ['usb', 'serial', 'windows']))
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-full px-3">
                        <label class="block uppercase tracking-wide text-grey-darker text-xs font-bold mb-2 dark:text-gray-300" for="path">
                            Path / Share Name
                        </label>
                        <input wire:model.blur="path" id="path" type="text" placeholder="{{ $type === 'usb' ? '/dev/usb/lp0' : ($type === 'serial' ? 'COM1' : 'MySharedPrinter') }}" class="appearance-none block w-full bg-grey-lighter text-grey-darker border @error('path') border-red-500 @else border-grey-lighter @enderror rounded py-3 px-4 mb-3 dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-300">
                        @error('path') <p class="text-red-500 text-xs italic dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endif

            <div class="mt-4">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                    Update Printer
                </button>
                <a href="{{ route('printers.index') }}" wire:navigate class="ml-4 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-100 disabled:opacity-25 transition dark:bg-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-600">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
