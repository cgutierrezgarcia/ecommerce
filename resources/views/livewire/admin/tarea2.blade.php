<div>
    <div>
        <x-slot name="header">
            <div class="flex items-center">
                <h2 class="font-semibold text-xl text-gray-600 leading-tight">
                    Lista de productos
                </h2>

                <x-button-link class="ml-auto" href="{{route('admin.products.create')}}">
                    Agregar producto
                </x-button-link>
            </div>
        </x-slot>
    </div>

    <x-table-responsive>
        <div class="flex bg-gray-200">
            <div class="px-6 py-4 w-1/3">
                <x-jet-input class="w-full"
                             dusk="search"
                             wire:model="search"
                             type="text"
                             placeholder="Introduzca el nombre del producto a buscar" />
            </div>

            <div class="relative py-5">
                <x-button wire:click="$toggle('show')" color="{{ $show ? 'red' : 'orange' }}" class="ml-auto">Mostrar/ocultar columnas</x-button>
                <div class="{{ $show ? '' : 'hidden' }} absolute grid bg-white rounded-md p-4 mt-4 shadow-2xl">
                    <x-button wire:click="$toggle('idSH')" color="{{ $idSH ? 'red' : 'green' }}" class="mb-2">ID</x-button>
                    <x-button wire:click="$toggle('nameSH')" color="{{ $nameSH ? 'red' : 'green' }}" class="mb-2">Nombre</x-button>
                    <x-button wire:click="$toggle('slugSH')" color="{{ $slugSH ? 'red' : 'green' }}" class="mb-2">Slug</x-button>
                    <x-button wire:click="$toggle('descriptionSH')" color="{{ $descriptionSH ? 'red' : 'green' }}" class="mb-2">Descripción</x-button>
                    <x-button wire:click="$toggle('categorySH')" color="{{ $categorySH ? 'red' : 'green' }}" class="mb-2">Categoría</x-button>
                    <x-button wire:click="$toggle('subcategorySH')" color="{{ $subcategorySH ? 'red' : 'green' }}" class="mb-2">Subcategoría</x-button>
                    <x-button wire:click="$toggle('brandSH')" color="{{ $brandSH ? 'red' : 'green' }}" class="mb-2">Marca</x-button>
                    <x-button wire:click="$toggle('statusSH')" color="{{ $statusSH ? 'red' : 'green' }}" class="mb-2">Estado</x-button>
                    <x-button wire:click="$toggle('priceSH')" color="{{ $priceSH ? 'red' : 'green' }}" class="mb-2">Precio</x-button>
                    <x-button wire:click="$toggle('colorSH')" color="{{ $colorSH ? 'red' : 'green' }}" class="mb-2">Color y cantidad</x-button>
                    <x-button wire:click="$toggle('sizeSH')" color="{{ $sizeSH ? 'red' : 'green' }}" class="mb-2">Talla y cantidad</x-button>
                    <x-button wire:click="$toggle('stockSH')" color="{{ $stockSH ? 'red' : 'green' }}" class="mb-2">Cantidad total</x-button>
                    <x-button wire:click="$toggle('createdAtSH')" color="{{ $createdAtSH ? 'red' : 'green' }}" class="mb-2">Fecha de creación</x-button>
                    <x-button wire:click="$toggle('updatedAtSH')" color="{{ $updatedAtSH ? 'red' : 'green' }}">Fecha de actualización</x-button>
                </div>
            </div>

            <div class="relative py-5 ml-4">
                <x-button wire:click="$toggle('showFilters')" color="{{ $show ? 'red' : 'orange' }}">Filtros</x-button>
            </div>
        </div>

        <div class="{{ $showFilters ? '' : 'hidden' }} bg-gray-200">
            <div class="px-6 py-4 w-1/3">
                <x-jet-label value="Categoría" />
                <x-jet-input wire:model="categorySearch"
                             type="text"
                             placeholder="Categoría" />
            </div>

            <div class="px-6 py-4 w-1/3">
                <x-jet-label value="Subcategoría" />
                <x-jet-input wire:model="subcategorySearch"
                             type="text"
                             placeholder="Subcategoría" />
            </div>

            <div class="px-6 py-4 w-1/3">
                <x-jet-label value="Marca" />
                <x-jet-input wire:model="brandSearch"
                             type="text"
                             placeholder="Marca" />
            </div>

            <div class="p-4">
                <x-jet-label value="Estado" />
                <select class="form-control" wire:model="status">
                    <option value="" selected>Cualquiera</option>
                    <option value="1">BORRADOR</option>
                    <option value="2">PUBLICADO</option>
                </select>
            </div>

            <div class="px-6 py-4 w-1/3">
                <x-jet-label value="Precio" />
                <x-jet-input wire:model="priceSearch"
                             type="text"
                             placeholder="Precio" />
            </div>

            <div class="{{ $sizeFilter ? 'hidden' : '' }}">
                <x-button wire:click="$toggle('colorsFilter')" color="{{ $colorsFilter ? 'green' : 'red' }}" class="ml-auto">colores</x-button>
            </div>

            <div class="{{ $colorsFilter ? 'hidden' : '' }}">
                <x-button wire:click="$toggle('sizeFilter')" color="{{ $sizeFilter ? 'green' : 'red' }}" class="ml-auto">Tallas</x-button>
            </div>

            <x-jet-button class="mt-4" wire:click="resetFilters">
                Eliminar Filtros
            </x-jet-button>
        </div>

         @if($products->count())
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th wire:click="orderByColumn('id')" scope="col" class="{{ $idSH ? 'hidden' : '' }} cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID
                    </th>
                    <th wire:click="orderByColumn('name')" scope="col" class="{{ $nameSH ? 'hidden' : '' }} cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nombre
                    </th>
                    <th wire:click="orderByColumn('slug')" scope="col" class="{{ $slugSH ? 'hidden' : '' }} cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Slug
                    </th>
                    <th wire:click="orderByColumn('description')" scope="col" class="{{ $descriptionSH ? 'hidden' : '' }} cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Descripción
                    </th>
                    <th scope="col" class="{{ $categorySH ? 'hidden' : '' }} px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Categoría
                    </th>
                    <th wire:click="orderByColumn('subcategory_id')" scope="col" class="{{ $subcategorySH ? 'hidden' : '' }} cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Subcategoría
                    </th>
                    <th wire:click="orderByColumn('brand_id')" scope="col" class="{{ $brandSH ? 'hidden' : '' }} cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Marca
                    </th>
                    <th wire:click="orderByColumn('status')" scope="col" class="{{ $statusSH ? 'hidden' : '' }} cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th wire:click="orderByColumn('price')" scope="col" class="{{ $priceSH ? 'hidden' : '' }} cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Precio
                    </th>
                    <th scope="col" class="{{ $colorSH ? 'hidden' : '' }} px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Color y cantidad
                    </th>
                    <th scope="col" class="{{ $sizeSH ? 'hidden' : '' }} px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Talla y cantidad
                    </th>
                    <th wire:click="orderByColumn('quantity')" scope="col" class="{{ $stockSH ? 'hidden' : '' }} cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Cantidad total
                    </th>
                    <th wire:click="orderByColumn('created_at')" scope="col" class="{{ $createdAtSH ? 'hidden' : '' }} cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Fecha de creación
                    </th>
                    <th wire:click="orderByColumn('updated_at')" scope="col" class="{{ $updatedAtSH ? 'hidden' : '' }} cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Fecha de actualización
                    </th>
                    <th scope="col" class="{{ $editSH ? 'hidden' : '' }} relative px-6 py-3">
                        <span class="sr-only">Editar</span>
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($products as $product)
                    <tr>
                        <td class="{{ $idSH ? 'hidden' : '' }} px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->id }}</div>
                        </td>
                        <td class="{{ $nameSH ? 'hidden' : '' }} px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 object-cover">
                                    <img class="h-10 w-10 rounded-full" src="{{ $product->images->count() ? Storage::url($product->images->first()->url) : 'img/default.png' }}" alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $product->name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="{{ $slugSH ? 'hidden' : '' }} px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->slug }}</div>
                        </td>
                        <td class="{{ $descriptionSH ? 'hidden' : '' }} px-6 py-4 block w-72 h-24 overflow-y-auto">
                            <div class="text-sm text-gray-900">{{ $product->description }}</div>
                        </td>
                        <td class="{{ $categorySH ? 'hidden' : '' }} px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->subcategory->category->name }}</div>
                        </td>
                        <td class="{{ $subcategorySH ? 'hidden' : '' }} px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->subcategory->name }}</div>
                        </td>
                        <td class="{{ $brandSH ? 'hidden' : '' }} px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->brand->name }}</div>
                        </td>
                        <td class="{{ $statusSH ? 'hidden' : '' }} px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $product->status == 1 ? 'red' : 'green' }}-100 text-{{ $product->status == 1 ? 'red' : 'green' }}-800">
                                {{ $product->status == 1 ? 'Borrador' : 'Publicado' }}
                            </span>
                        </td>
                        <td class="{{ $priceSH ? 'hidden' : '' }} px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->price }} &euro;
                        </td>
                        <td class="{{ $colorSH ? 'hidden' : '' }} px-6 py-4 whitespace-nowrap text-sm text-gray-500 block h-24 overflow-x-auto overflow-y-auto">
                            @if ($product->subcategory->size)
                                @foreach($product->sizes as $size)
                                    <span class="font-bold">{{ $size->name }}</span><br/>
                                    @foreach($size->colors as $color)
                                        {{ __(ucfirst($color->name)) }} ({{ $color->pivot->quantity }})<br/>
                                    @endforeach
                                    <br/>
                                @endforeach
                            @elseif($product->subcategory->color)
                                @foreach($product->colors as $color)
                                    {{ __(ucfirst($color->name)) }} ({{ $color->pivot->quantity }})<br/>
                                @endforeach
                            @else
                                <br/>No tiene
                            @endif
                        </td>
                        <td class="{{ $sizeSH ? 'hidden' : '' }} px-6 py-4 whitespace-nowrap text-sm text-gray-500 h-24 overflow-x-auto overflow-y-auto">
                            @if ($product->subcategory->size)
                                @foreach($product->sizes as $size)
                                    @php
                                    $quantity = 0;
                                    foreach($size->colors as $color) {
                                        $quantity += $color->pivot->quantity;
                                    }
                                    @endphp
                                    {{ $size->name }} ({{ $quantity }})<br/>
                                @endforeach
                            @else
                                No tiene
                            @endif
                        </td>
                        <td class="{{ $stockSH ? 'hidden' : '' }} px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if ($product->subcategory->size)
                                {{ $product->stock }}
                            @elseif($product->subcategory->color)
                                {{ $product->stock }}
                            @else
                                {{ $product->quantity }}
                            @endif
                        </td>
                        <td class="{{ $createdAtSH ? 'hidden' : '' }} px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->created_at }}</div>
                        </td>
                        <td class="{{ $updatedAtSH ? 'hidden' : '' }} px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->updated_at }}</div>
                        </td>
                        <td class="{{ $editSH ? 'hidden' : '' }} px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="px-6 py-4">
                No existen productos coincidentes
            </div>
        @endif

        <div class="flex">
            <div class="p-4">
                <select class="form-control" wire:model="paginate">
                    <option value="5">Mostrar 5 productos</option>
                    <option value="10" selected>Mostrar 10 productos</option>
                    <option value="25">Mostrar 25 productos</option>
                    <option value="50">Mostrar 50 productos</option>
                    <option value="100">Mostrar 100 productos</option>
                </select>
            </div>

            @if($products->hasPages())
                <div class="px-6 py-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

    </x-table-responsive>
</div>
