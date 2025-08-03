<div>
    <div class="mb-4">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">QR Code Reader</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">Upload an image containing a QR code to scan and read its contents</p>
    </div>

    <div class="mt-6 space-y-6">
        <!-- Upload Section -->
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 dark:bg-zinc-900 dark:border dark:border-zinc-700">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Upload QR Code Image</h2>
            </div>

            <div class="space-y-4">
                <!-- File Upload -->
                <div>
                    <label class="block uppercase tracking-wide text-grey-darker text-xs font-bold mb-2 dark:text-gray-300" for="qr-file">
                        Select QR Code Image
                    </label>
                    <input
                        id="qr-file"
                        type="file"
                        wire:model="qrFile"
                        accept="image/*"
                        class="appearance-none block w-full bg-grey-lighter text-grey-darker border border-grey-lighter rounded py-3 px-4 mb-3 dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-300"
                    />
                    @error('qrFile') <p class="text-red-500 text-xs italic dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Preview uploaded image -->
                @if ($qrFile)
                    <div>
                        <label class="block uppercase tracking-wide text-grey-darker text-xs font-bold mb-2 dark:text-gray-300">
                            Preview
                        </label>
                        <div class="mt-2">
                            <img src="{{ $qrFile->temporaryUrl() }}" alt="QR Code Preview" class="max-w-32 max-h-32 object-contain rounded-lg border border-gray-200 dark:border-zinc-700">
                        </div>
                    </div>
                @endif

                <!-- Scan Button -->
                <div class="flex gap-3 mt-2">
                    <button
                        wire:click="scanQrCode"
                        wire:loading.attr="disabled"
                        wire:target="scanQrCode"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-blue-800"
                        {{ !$qrFile || $isScanning ? 'disabled' : '' }}
                    >
                        <span wire:loading.remove wire:target="scanQrCode">
                            Scan QR Code
                        </span>
                        <span wire:loading wire:target="scanQrCode">
                            Scanning...
                        </span>
                    </button>

                    @if ($scanResult || $error)
                        <button
                            wire:click="clearResult"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-200 disabled:opacity-25 transition dark:bg-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-600"
                        >
                            Clear
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Results Section -->
        @if ($scanResult || $error)
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 dark:bg-zinc-900 dark:border dark:border-zinc-700">
                <div class="mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Scan Result</h2>
                </div>

                <div class="space-y-4">
                    @if ($error)
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative dark:bg-red-900/30 dark:border-red-500 dark:text-red-400" role="alert">
                            <span class="block sm:inline">{{ $error }}</span>
                        </div>
                    @endif

                    @if ($scanResult)
                        <div>
                            <label class="block uppercase tracking-wide text-grey-darker text-xs font-bold mb-2 dark:text-gray-300">
                                QR Code Content
                            </label>
                            <div class="mt-2">
                                <textarea
                                    readonly
                                    rows="6"
                                    class="appearance-none block w-full bg-grey-lighter text-grey-darker border border-grey-lighter rounded py-3 px-4 font-mono text-sm dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-300"
                                >{{ $scanResult }}</textarea>
                            </div>
                        </div>

                        <!-- Additional actions for the result -->
                        <div class="flex gap-3">
                            <button
                                onclick="navigator.clipboard.writeText('{{ addslashes($scanResult) }}')"
                                class="inline-flex items-center px-3 py-1 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 active:bg-gray-100 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 disabled:opacity-25 transition dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-700"
                            >
                                Copy to Clipboard
                            </button>

                            @if (filter_var($scanResult, FILTER_VALIDATE_URL))
                                <a
                                    href="{{ $scanResult }}"
                                    target="_blank"
                                    class="inline-flex items-center px-3 py-1 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 active:bg-gray-100 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 disabled:opacity-25 transition dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-700"
                                >
                                    Open Link
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Instructions -->
        <div class="bg-gray-50 shadow-md rounded px-8 pt-6 pb-8 mb-4 dark:bg-zinc-800 dark:border dark:border-zinc-700">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Instructions</h2>
            </div>

            <div class="prose prose-sm dark:prose-invert">
                <ul>
                    <li class="dark:text-gray-300">Select an image file containing a QR code (JPEG, PNG, JPG, GIF formats supported)</li>
                    <li class="dark:text-gray-300">Maximum file size: 2MB</li>
                    <li class="dark:text-gray-300">Ensure the QR code is clearly visible and not blurred</li>
                    <li class="dark:text-gray-300">The scan result will display the decoded content from the QR code</li>
                </ul>
            </div>
        </div>
    </div>
</div>
