            <div data-pos-results>
                @if ($products->isEmpty()) <div class="border-t"><x-ui.table-state :state="request()->filled('q') ? 'filtered' : 'empty'" title="Produk tidak ditemukan" /></div>
                @else <div class="grid gap-4 border-t p-6 sm:grid-cols-2">
                    @foreach ($products as $product)
                        <button type="button" class="card cursor-pointer overflow-hidden text-left focus-visible:ring-2 focus-visible:ring-ring/50" data-cart-add data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->sell_price }}" data-stock="{{ $product->stock_qty }}" data-image="{{ $product->image_path ? $product->image_url : '' }}" aria-label="Tambah {{ $product->name }} ke keranjang">
                            @if ($product->image_path)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-36 w-full object-cover" loading="lazy">
                            @else
                                <span class="flex h-36 items-center justify-center bg-soft text-soft-foreground"><x-ui.icon name="package" class="h-10 w-10" /></span>
                            @endif
                            <span class="block p-4">
                                <span class="block text-xs text-muted-foreground">{{ $product->category->name }} · {{ $product->sku }}</span>
                                <span class="block font-semibold">{{ $product->name }}</span>
                                <span class="mt-3 flex items-center justify-between gap-2"><span class="font-semibold tabular-nums">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</span><x-ui.badge variant="success">Stok {{ $product->stock_qty }}</x-ui.badge></span>
                                <span class="btn btn-outline btn-sm mt-3 w-full"><x-ui.icon name="cart" />Tambah ke keranjang</span>
                            </span>
                        </button>
                    @endforeach
                </div>@endif
                <x-ui.pagination :paginator="$products" />
            </div>
