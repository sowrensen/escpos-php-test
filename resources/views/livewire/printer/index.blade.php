<div>
    <div class="mb-4">
        <h1 class="text-2xl font-semibold">Printers</h1>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="mb-4 flex justify-between items-center">
        <div class="w-1/3">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search printers..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900 placeholder-gray-400 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 py-2 px-2">
        </div>
        <a href="{{ route('printers.create') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
            Create New Printer
        </a>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CPL</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Connection</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($printers as $printer)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $printer->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::ucfirst($printer->type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $printer->characters_per_line }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if ($printer->ip_address && $printer->port)
                                {{ $printer->ip_address }}:{{ $printer->port }}
                            @elseif ($printer->path)
                                {{ $printer->path }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('printers.edit', $printer) }}" wire:navigate class="inline-block py-1 px-2 text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 dark:text-blue-300 dark:bg-blue-700 dark:hover:bg-blue-600">Edit</a>
                            <button wire:click="initiateTestPrint({{ $printer->id }})" class="ml-2 py-1 px-2 text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 dark:text-blue-300 dark:bg-blue-700 dark:hover:bg-blue-600 cursor-pointer">Test Print</button>
                            <button wire:click="delete({{ $printer->id }})" wire:confirm="Are you sure you want to delete this printer?" class="ml-2 py-1 px-2 text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 dark:text-red-300 dark:bg-red-700 dark:hover:bg-red-600 cursor-pointer">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No printers found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $printers->links() }}
    </div>

    @script
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('print-receipt-content', (event) => {
                let receiptContent = event.content;
                if (!receiptContent) {
                    console.error('No content received for printing.');
                    return;
                }

                // Create a hidden iframe
                const iframe = document.createElement('iframe');
                iframe.style.position = 'absolute';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.border = '0';
                iframe.style.visibility = 'hidden';

                document.body.appendChild(iframe);

                // Write the content to the iframe
                // Using <pre> for monospaced font and preserving whitespace
                // Basic styling for receipt look
                iframe.contentDocument.open();
                iframe.contentDocument.write(`
                    <html>
                    <head>
                        <title>Print Receipt</title>
                        <style>
                            body {
                                font-family: monospace;
                                font-size: 10pt; /* Adjust as needed */
                                margin: 5mm; /* Adjust printer margins */
                                width: auto; /* Allow browser to determine width for printing */
                            }
                            pre {
                                white-space: pre-wrap; /* CSS3 */
                                white-space: -moz-pre-wrap; /* Mozilla, since 1999 */
                                white-space: -pre-wrap; /* Opera 4-6 */
                                white-space: -o-pre-wrap; /* Opera 7 */
                                word-wrap: break-word; /* Internet Explorer 5.5+ */
                                margin: 0;
                                padding: 0;
                            }
                        </style>
                    </head>
                    <body>
                        <pre>${receiptContent}</pre>
                    </body>
                    </html>
                `);
                iframe.contentDocument.close();

                // Wait for iframe to load content before printing
                iframe.onload = function() {
                    try {
                        iframe.contentWindow.focus(); // Required for some browsers
                        iframe.contentWindow.print();
                    } catch (e) {
                        console.error('Error printing:', e);
                        alert('Could not initiate print. Please check console for errors.');
                    }
                    // Clean up: remove the iframe after printing or error
                    // Use a timeout to ensure print dialog has time to process
                    setTimeout(() => {
                        document.body.removeChild(iframe);
                    }, 1000);
                };
            });
        });
    </script>
    @endscript

</div>
