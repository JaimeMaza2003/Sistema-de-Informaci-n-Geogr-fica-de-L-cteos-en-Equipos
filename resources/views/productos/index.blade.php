@extends('layouts.app')

@section('title', 'Mi Catálogo de Productos Lácteos')

@push('styles')
<style>
    .products-hero {
        background: linear-gradient(135deg, #095c43 0%, #0f7a5a 60%, #1a9970 100%);
        padding: 1.8rem 1.5rem 4.5rem;
        position: relative;
        overflow: hidden;
    }
    .products-hero::after {
        content: '';
        position: absolute;
        bottom: -1px; left: 0; right: 0;
        height: 40px;
        background: var(--bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .hero-title { font-family:'Poppins',sans-serif; font-weight:800; font-size:1.6rem; color:#fff; margin-bottom:.3rem; }
    .hero-sub   { color:rgba(255,255,255,.75); font-size:.9rem; }
    .hero-pill  { display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.18);color:#fff;border-radius:20px;padding:.3rem .85rem;font-size:.78rem;font-weight:600; }

    /* ---- PRODUCT CARD ---- */
    .product-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        transition: transform .22s, box-shadow .22s;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }
    .product-card-top {
        background: linear-gradient(135deg, var(--primary-lt) 0%, #f0f9f5 100%);
        padding: 1.2rem 1.1rem;
        position: relative;
    }
    .product-emoji {
        font-size: 2.4rem;
        margin-bottom: .5rem;
        display: block;
    }
    .product-name {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 1rem;
        color: var(--text);
        margin-bottom: .15rem;
    }
    .product-pres { font-size:.8rem; color:var(--text-muted); }

    .product-card-body {
        padding: .9rem 1rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .price-box {
        background: var(--bg);
        border-radius: 10px;
        padding: .7rem .9rem;
        margin-bottom: .9rem;
        flex: 1;
    }
    .price-main {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--primary);
        line-height: 1;
    }
    .price-label { font-size:.7rem; color:var(--text-muted); font-weight:500; margin-bottom:.1rem; }
    .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: .8rem;
        padding: .22rem 0;
        border-bottom: 1px solid var(--border);
        gap:.5rem;
    }
    .price-row:last-child { border-bottom: none; }
    .price-row-label { color: var(--text-muted); }
    .price-row-val { font-weight: 600; color: var(--text); }

    .product-card-actions {
        display: flex;
        flex-direction: column;
        gap: .4rem;
    }
    .btn-find {
        background: var(--accent);
        color: var(--text);
        border: none;
        border-radius: 10px;
        padding: .55rem .9rem;
        font-weight: 700;
        font-size: .88rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        text-decoration: none;
        transition: filter .18s, transform .15s;
    }
    .btn-find:hover { filter: brightness(1.08); transform: translateY(-1px); color: var(--text); }
    .btn-pdf {
        background: transparent;
        color: var(--text-muted);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: .42rem .9rem;
        font-weight: 600;
        font-size: .82rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        text-decoration: none;
        transition: all .18s;
    }
    .btn-pdf:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-lt); }

    .frio-badge {
        position: absolute;
        top: .75rem; right: .75rem;
        background: rgba(14,165,233,.12);
        color: #0369a1;
        border-radius: 20px;
        padding: .2em .6em;
        font-size: .7rem;
        font-weight: 700;
    }
    .categoria-badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        background: rgba(15,122,90,.12);
        color: var(--primary);
        border-radius: 20px;
        padding: .2em .65em;
        font-size: .72rem;
        font-weight: 700;
        margin-bottom: .4rem;
    }

    /* ---- EMPTY ---- */
    .empty-products {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 5rem 2rem;
        color: var(--text-muted);
        text-align: center;
    }
    .empty-products .big-emoji { font-size: 4rem; opacity: .4; margin-bottom: 1rem; }

    /* ---- EMOJI POR CATEGORIA ---- */
    .emoji-leche { }
    .emoji-queso { }
</style>
@endpush

@section('content')

<!-- ===== HERO ===== -->
<div class="products-hero">
    <div class="container px-3 px-lg-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <span class="hero-pill"><i class="bi bi-box-seam-fill"></i> Mi Catálogo</span>
                    <span class="hero-pill"><i class="bi bi-collection-fill"></i> {{ count($productos) }} productos</span>
                </div>
                <h1 class="hero-title">Catálogo de Productos Lácteos</h1>
                <p class="hero-sub">Gestiona tu oferta, genera fichas técnicas y encuentra los mejores puntos de venta para tus productos.</p>
            </div>
            <div class="d-flex gap-2 mt-1">
                <a href="{{ route('productos.catalogo-publico', auth()->id() ?? 2) }}" target="_blank"
                   style="background:rgba(255,255,255,.18);color:#fff;border:1.5px solid rgba(255,255,255,.4);border-radius:10px;padding:.5rem 1rem;font-weight:600;font-size:.85rem;display:flex;align-items:center;gap:.4rem;text-decoration:none;backdrop-filter:blur(4px);transition:background .2s;"
                   onmouseover="this.style.background='rgba(255,255,255,.28)'" onmouseout="this.style.background='rgba(255,255,255,.18)'">
                    <i class="bi bi-share-fill"></i> Compartir
                </a>
                <a href="{{ route('productos.create') }}"
                   style="background:var(--accent);color:var(--text);border:none;border-radius:10px;padding:.5rem 1.1rem;font-weight:700;font-size:.88rem;display:flex;align-items:center;gap:.4rem;text-decoration:none;transition:filter .2s;"
                   onmouseover="this.style.filter='brightness(1.07)'" onmouseout="this.style.filter='brightness(1)'">
                    <i class="bi bi-plus-circle-fill"></i> Nuevo Producto
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container px-3 px-lg-4">

    @php
    $emojiMap = [
        'Leche'      => '🥛',
        'Queso'      => '🧀',
        'Yogurt'     => '🍶',
        'Mantequilla'=> '🧈',
        'Crema'      => '🍦',
        'default'    => '🧴',
    ];
    @endphp

    @forelse($productos as $prod)
        @if($loop->first)
        <div class="row g-4">
        @endif

        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="product-card">

                <div class="product-card-top">
                    @if($prod->requiere_refrigeracion)
                        <span class="frio-badge"><i class="bi bi-snow me-1"></i>{{ $prod->temperatura_conservacion }}</span>
                    @endif
                    <span class="product-emoji">{{ $emojiMap[$prod->categoria_nombre] ?? $emojiMap['default'] }}</span>
                    <span class="categoria-badge">
                        <i class="bi bi-tag-fill"></i>{{ $prod->categoria_nombre }}
                    </span>
                    <div class="product-name">{{ $prod->nombre }}</div>
                    <div class="product-pres">{{ $prod->presentacion }}</div>
                </div>

                <div class="product-card-body">
                    <div class="price-box">
                        <div class="price-label">Precio al comercio</div>
                        <div class="price-main">${{ number_format($prod->precio_comercio, 2) }}</div>
                        <div class="mt-2">
                            <div class="price-row">
                                <span class="price-row-label">PVP Sugerido</span>
                                <span class="price-row-val">${{ number_format($prod->precio_sugerido, 2) }}</span>
                            </div>
                            <div class="price-row">
                                <span class="price-row-label">Pedido mínimo</span>
                                <span class="price-row-val">{{ $prod->pedido_minimo }}</span>
                            </div>
                            <div class="price-row">
                                <span class="price-row-label">Cap. semanal</span>
                                <span class="price-row-val">{{ $prod->capacidad_semanal }}</span>
                            </div>
                            <div class="price-row">
                                <span class="price-row-label">Vida útil</span>
                                <span class="price-row-val">{{ $prod->vida_util_dias }} días</span>
                            </div>
                        </div>
                    </div>

                    <div class="product-card-actions">
                        <a href="{{ route('productos.compatibles', $prod->id) }}" class="btn-find">
                            <i class="bi bi-compass-fill"></i> Buscar dónde ofrecerlo
                        </a>
                        <a href="{{ route('productos.ficha-pdf', $prod->id) }}" target="_blank" class="btn-pdf">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Ficha Comercial PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if($loop->last)
        </div>
        @endif

    @empty
        <div class="empty-products">
            <div class="big-emoji">🧴</div>
            <h4 class="fw-bold" style="color:var(--text);">Aún no tienes productos</h4>
            <p class="mb-4" style="max-width:420px;">Registra tus quesos, leches, yogurts o mantequillas para encontrar tiendas y supermercados compatibles en Latacunga.</p>
            <a href="{{ route('productos.create') }}" class="btn-primary-sig">
                <i class="bi bi-plus-circle-fill me-1"></i> Registrar mi primer producto
            </a>
        </div>
    @endforelse

</div>
@endsection
