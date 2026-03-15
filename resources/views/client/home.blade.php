@extends('client.layouts.app')
@section('title', 'PBall - Official Store')

@push('styles')
<style>
    /* Ép tỷ lệ ảnh dọc chuẩn Lookbook / Leninn Style */
    .aspect-4-5 { aspect-ratio: 4 / 5; }
    
    /* Gạch chân mượt mà khi hover tên sản phẩm */
    .hover-underline {
        background-image: linear-gradient(transparent calc(100% - 1px), #000 1px);
        background-repeat: no-repeat;
        background-size: 0% 100%;
        transition: background-size 0.3s ease;
    }
    .group:hover .hover-underline {
        background-size: 100% 100%;
    }
</style>
@endpush

@section('content')
<div class="w-full bg-white">
    
    <div class="relative w-full h-[70vh] md:h-screen mb-16 md:mb-24">
        <img src="https://images.unsplash.com/photo-1698656641889-408920d367af?q=80&w=2070&auto=format&fit=crop" 
             alt="PBall SS26 Campaign" 
             class="w-full h-full object-cover">
        
        <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/10">
            <h1 class="text-white text-5xl md:text-7xl font-black uppercase tracking-tighter mb-4 drop-shadow-lg">
                PBALL SS26
            </h1>
            <a href="#" class="text-white text-sm font-bold uppercase tracking-widest border-b border-white pb-1 hover:text-gray-200 transition-colors">
                Khám phá ngay
            </a>
        </div>
    </div>

    @foreach($categories as $category)
        @if($category->products->count() > 0)
        <section class="mb-20 md:mb-32 px-4 md:px-12 max-w-[1600px] mx-auto">
            
            <div class="flex justify-between items-end mb-8 md:mb-12">
                <h2 class="text-lg md:text-xl font-extrabold uppercase tracking-widest text-black border-b-2 border-black pb-1">
                    {{ $category->name }}
                </h2>
                <a href="#" class="text-xs font-semibold text-gray-500 uppercase tracking-widest hover:text-black transition-colors">
                    Xem tất cả &rarr;
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 md:gap-x-6 gap-y-10 md:gap-y-16">
                @foreach($category->products as $product)
                
                @php
                    $primaryImg = $product->images->first();
                    $secondaryImg = $product->images->skip(1)->first();
                    $img_url = $primaryImg ? asset($primaryImg->image_path) : 'https://via.placeholder.com/600x800?text=No+Image';
                    $img2_url = $secondaryImg ? asset($secondaryImg->image_path) : null;
                    
                    $hasSale = $product->sale_price && $product->sale_price < $product->price;
                    $displayPrice = $hasSale ? $product->sale_price : $product->price;
                @endphp

                <a href="#" class="group block cursor-pointer relative">
                    
                    <div class="relative aspect-4-5 bg-[#f4f4f4] mb-4 overflow-hidden">
                        
                        <div class="absolute top-3 left-3 z-20 flex flex-col gap-1.5">
                            @if($product->stock <= 0) 
                                <span class="bg-black text-white text-[10px] font-bold px-2 py-1 uppercase tracking-widest">Hết hàng</span>
                            @endif
                            @if($hasSale)
                                <span class="bg-red-600 text-white text-[10px] font-bold px-2 py-1 uppercase tracking-widest">Sale</span>
                            @endif
                        </div>

                        <img src="{{ $img_url }}" alt="{{ $product->name }}" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 ease-in-out {{ $img2_url ? 'group-hover:opacity-0' : '' }}">

                        @if($img2_url)
                            <img src="{{ $img2_url }}" alt="{{ $product->name }}" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 ease-in-out opacity-0 group-hover:opacity-100">
                        @endif
                    </div>
                    
                    <div class="flex flex-col text-left">
                        <h3 class="text-[13px] md:text-[14px] font-semibold text-gray-900 uppercase tracking-wide mb-1.5 line-clamp-1 hover-underline w-fit">
                            {{ $product->name }}
                        </h3>
                        
                        <div class="flex items-center gap-3 text-[13px] md:text-[14px] tracking-wider">
                            @if($hasSale)
                                <span class="text-red-600 font-bold">{{ number_format($product->sale_price, 0, ',', '.') }} ₫</span>
                                <span class="text-gray-400 line-through">{{ number_format($product->price, 0, ',', '.') }} ₫</span>
                            @else
                                <span class="text-black font-bold">{{ number_format($product->price, 0, ',', '.') }} ₫</span>
                            @endif
                        </div>
                    </div>

                </a>
                @endforeach
            </div>
        </section>
        @endif
    @endforeach

</div>
@endsection