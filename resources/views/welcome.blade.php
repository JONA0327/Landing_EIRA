<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>4Life Bienestar Integral - Transforma Tu Vida</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8f9fa; color: #1f2937; line-height: 1.6; }
        a { color: inherit; text-decoration: none; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        .btn-primary { padding: 14px 32px; border-radius: 8px; border: none; font: 600 15px 'Poppins', sans-serif; cursor: pointer; transition: all .3s ease; background: linear-gradient(135deg, #0891b2, #10b981); color: #fff; box-shadow: 0 4px 12px rgba(8, 145, 178, .25); }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(8, 145, 178, .4); }
        .btn-primary:active { transform: translateY(-1px); }
        .btn-secondary { padding: 14px 32px; border-radius: 8px; border: 2px solid #0891b2; background: transparent; color: #0891b2; font: 600 15px 'Poppins', sans-serif; cursor: pointer; transition: all .3s ease; }
        .btn-secondary:hover { background: #0891b2; color: #fff; transform: translateY(-2px); }
        .btn-secondary:active { transform: translateY(0); }

        .gradient-cyan-lime { background: linear-gradient(135deg, #06b6d4 0%, #10b981 50%, #84cc16 100%); }
        .text-white { color: #fff; }
        .text-center { text-align: center; }
        .fw6 { font-weight: 600; } .fw7 { font-weight: 700; } .fw8 { font-weight: 800; }
        .fs14 { font-size: 14px; } .fs16 { font-size: 16px; } .fs18 { font-size: 18px; } .fs20 { font-size: 20px; }
        .fs24 { font-size: 24px; } .fs28 { font-size: 28px; } .fs32 { font-size: 32px; } .fs40 { font-size: 40px; } .fs48 { font-size: 48px; }
        .gap8 { gap: 8px; } .gap12 { gap: 12px; } .gap16 { gap: 16px; } .gap24 { gap: 24px; } .gap32 { gap: 32px; }
        .mt16 { margin-top: 16px; } .mt24 { margin-top: 24px; } .mt32 { margin-top: 32px; } .mt48 { margin-top: 48px; }
        .mb16 { margin-bottom: 16px; } .mb24 { margin-bottom: 24px; } .mb32 { margin-bottom: 32px; } .mb48 { margin-bottom: 48px; }
        .p16 { padding: 16px; } .p24 { padding: 24px; } .p32 { padding: 32px; } .p48 { padding: 48px; }
        .fx { display: flex; } .col { flex-direction: column; } .wrap { flex-wrap: wrap; }
        .ac { align-items: center; } .jc { justify-content: center; } .jb { justify-content: space-between; }
        .w100 { width: 100%; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0, 0, 0, .08); transition: all .3s ease; overflow: hidden; }
        .card:hover { transform: translateY(-8px); box-shadow: 0 12px 28px rgba(0, 0, 0, .12); }
        .section-title { font: 800 36px 'Poppins', sans-serif; color: #1f2937; margin-bottom: 16px; line-height: 1.2; }
        .section-subtitle { font: 400 16px 'Inter'; color: #6b7280; margin-bottom: 48px; }

        .productos-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 32px; }
        .producto-card { background: #fff; border-radius: 16px; border: 1px solid #eef1f4; box-shadow: 0 2px 10px rgba(15, 23, 42, .05); overflow: hidden; display: flex; flex-direction: column; transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; }
        .producto-card:hover { transform: translateY(-6px); box-shadow: 0 18px 34px rgba(15, 23, 42, .1); border-color: #cffafe; }
        .producto-card-img-wrap { width: 100%; height: 210px; background: linear-gradient(180deg, #f8fafc, #eef2f5); overflow: hidden; }
        .producto-card-img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .producto-card-body { padding: 26px; display: flex; flex-direction: column; flex: 1; text-align: left; }
        .producto-card-titulo { font: 700 20px 'Poppins', sans-serif; color: #0f172a; margin-bottom: 10px; }
        .producto-card-intro { color: #6b7280; font-size: 14px; line-height: 1.65; text-align: justify; margin-bottom: 16px; }
        .producto-card-lista { list-style: none; margin: 0 0 20px; padding: 0; }
        .producto-card-lista li { display: flex; align-items: flex-start; gap: 10px; color: #4b5563; font-size: 13.5px; line-height: 1.55; margin-bottom: 9px; }
        .producto-card-lista li svg { flex-shrink: 0; margin-top: 3px; }
        .precio-pill { display: inline-block; align-self: flex-start; background: linear-gradient(135deg, rgba(8,145,178,.1), rgba(16,185,129,.12)); color: #0891b2; font: 700 15px 'Poppins', sans-serif; padding: 6px 16px; border-radius: 999px; margin-bottom: 18px; }
        .producto-card-categoria { display: inline-block; align-self: flex-start; background: #ecfdf5; color: #059669; font: 600 11px 'Poppins', sans-serif; letter-spacing: .03em; text-transform: uppercase; padding: 4px 11px; border-radius: 999px; margin-bottom: 8px; }

        #map { height: 400px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0, 0, 0, .1); overflow: hidden; }

        @media (max-width: 768px) {
            .section-title { font-size: 28px; }
            .section-subtitle { font-size: 14px; margin-bottom: 32px; }
            .fs48 { font-size: 32px; }
            .fs40 { font-size: 28px; }
            .fx.wrap > * { min-width: 100%; }
        }
    </style>
</head>
<body>

{{-- ── SELECTOR DE PAÍS ─────────────────────────────────────────────── --}}
<div id="region-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,.85); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px">
    <div style="background: #fff; border-radius: 16px; max-width: 480px; width: 100%; padding: 40px 32px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,.3)">
        <div style="margin-bottom: 12px; color: #0891b2">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" style="margin: 0 auto">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M12 3c2.4 2.5 3.7 5.6 3.7 9s-1.3 6.5-3.7 9c-2.4-2.5-3.7-5.6-3.7-9s1.3-6.5 3.7-9z"/>
            </svg>
        </div>
        <h2 style="font: 800 24px 'Poppins', sans-serif; color: #1f2937; margin-bottom: 8px">¿Desde qué país nos visitas?</h2>
        <p style="color: #6b7280; font-size: 14px; margin-bottom: 16px">Así te mostramos los productos, precios y contacto disponibles en tu región</p>

        <div id="region-botones" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px">
            @foreach ($regiones as $region)
                <button onclick="seleccionarRegion('{{ $region->slug }}')"
                        style="display: flex; align-items: center; gap: 10px; padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; background: #fff; cursor: pointer; font: 600 15px 'Poppins', sans-serif; color: #1f2937; transition: all .2s"
                        onmouseover="this.style.borderColor='#0891b2';this.style.background='#f0f9ff'"
                        onmouseout="this.style.borderColor='#e5e7eb';this.style.background='#fff'">
                    <img src="{{ asset('images/flags/' . $region->slug . '.png') }}" alt="{{ $region->nombre }}"
                         style="width: 32px; height: 24px; object-fit: cover; border-radius: 6px; box-shadow: 0 0 0 1px rgba(0,0,0,.08); flex-shrink: 0">
                    {{ $region->nombre }}
                </button>
            @endforeach
        </div>
    </div>
</div>

<button id="region-pill" onclick="mostrarSelectorRegion()"
        style="display: none; align-items: center; gap: 8px; position: fixed; top: 16px; right: 16px; z-index: 1000; padding: 8px 14px; background: #fff; border-radius: 999px; box-shadow: 0 4px 12px rgba(0,0,0,.15); font: 600 13px 'Poppins', sans-serif; color: #1f2937; border: none; cursor: pointer">
    <img id="region-pill-flag" src="" alt="" style="width: 24px; height: 18px; object-fit: cover; border-radius: 4px; box-shadow: 0 0 0 1px rgba(0,0,0,.08)">
    <span id="region-pill-nombre">Cambiar país</span>
</button>

{{-- ── HERO ─────────────────────────────────────────────────────────── --}}
<section class="p48" style="padding: 100px 20px 64px; color: #fff; position: relative; overflow: hidden; min-height: 60vh; display: flex; align-items: center; background: #065f46">
    <video autoplay muted loop playsinline
           style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0">
        <source src="{{ asset('videos/hero-plantas.mp4') }}" type="video/mp4">
    </video>
    <div class="gradient-cyan-lime" style="position: absolute; inset: 0; opacity: .78; z-index: 1"></div>

    <div class="container" style="position: relative; z-index: 2; max-width: 900px">
        <div class="text-center">
            <div style="font: 600 14px 'Poppins'; margin-bottom: 12px; opacity: .9; letter-spacing: 1px">BIENESTAR INTEGRAL</div>
            <h1 class="fs48" style="font-weight: 800; margin-bottom: 8px; line-height: 1.2">EIRA</h1>
            <p class="fs20" style="margin-bottom: 24px; opacity: .95">Con productos 4Life respaldados por 28+ años de investigación científica</p>
            <p class="fs14" style="opacity: .85">Entrega rápida • Garantía 100% • Orientación Personalizada</p>
        </div>
    </div>
</section>

{{-- ── CATEGORÍAS DESTACADAS ────────────────────────────────────────── --}}
<section class="p48" style="padding: 80px 20px; background: #fff">
    <div class="container">
        <h2 class="section-title" style="display: flex; align-items: center; gap: 12px">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="1.5" style="flex-shrink: 0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75a2.25 2.25 0 012.25-2.25h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
            </svg>
            Categorías Destacadas
        </h2>
        <p class="section-subtitle">Soluciones completas para cada aspecto de tu bienestar</p>

        <div class="productos-grid">
        @if ($productosDinamicos->isNotEmpty())
            {{-- Productos activados desde el panel admin (sincronizados del catálogo del CRM) --}}
            @foreach ($productosDinamicos as $producto)
                @php
                    // La descripción de IA trae: 1a línea = frase intro, siguientes = "• punto".
                    // Se separan para renderizar viñetas reales (ícono) en vez de "•" en texto plano.
                    $lineas = collect(preg_split('/\r\n|\r|\n/', $producto->descripcionLanding ?? ''))
                        ->map(fn ($l) => trim($l))
                        ->filter(fn ($l) => $l !== '')
                        ->values();
                    $intro = null;
                    $puntos = [];
                    foreach ($lineas as $linea) {
                        if (str_starts_with($linea, '•')) {
                            $puntos[] = trim(ltrim($linea, "• \t"));
                        } elseif ($intro === null) {
                            $intro = $linea;
                        } else {
                            $puntos[] = $linea;
                        }
                    }
                @endphp
                <div class="producto-card producto-region-card" data-regions="{{ $producto->regions->pluck('slug')->implode(',') }}">
                    <div class="producto-card-img-wrap">
                        @if ($producto->imagen)
                            <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}" class="producto-card-img">
                        @endif
                    </div>
                    <div class="producto-card-body">
                        @if ($producto->categoria)
                            <span class="producto-card-categoria">{{ $producto->categoria }}</span>
                        @endif
                        <h3 class="producto-card-titulo">{{ $producto->nombre }}</h3>
                        <div style="flex: 1">
                            @if ($intro)
                                <p class="producto-card-intro">{{ $intro }}</p>
                            @endif
                            @if (!empty($puntos))
                                <ul class="producto-card-lista">
                                    @foreach ($puntos as $punto)
                                        <li>
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            <span>{{ $punto }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            @if ($producto->precio)
                                <div class="precio-pill">{{ $producto->precio }}</div>
                            @endif
                        </div>
                        <div class="fx gap8">
                            <button class="btn-primary" style="flex: 1; font-size: 13px; padding: 12px 6px" onclick="abrirTienda()">Comprar</button>
                            <button class="btn-secondary" style="flex: 1; font-size: 13px; padding: 12px 6px" data-producto="{{ $producto->nombre }}" onclick="abrirWhatsappProducto(this)">Más info</button>
                        </div>
                    </div>
                </div>
            @endforeach
            <div id="sin-productos-region" style="display: none; grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: #6b7280">
                <p style="font-size: 15px">Aún no tenemos catálogo cargado para tu país —
                    <a href="#" onclick="abrirWhatsapp('Hola, me interesa saber más sobre los productos 4Life disponibles en mi país'); return false;" style="color: #0891b2; font-weight: 600; text-decoration: underline">contáctanos por WhatsApp</a>.
                </p>
            </div>
        @else
            <div class="producto-card">
                <div class="producto-card-img-wrap">
                    <img src="{{ asset('images/product-trifactor.png') }}" alt="4Life Trifactor" class="producto-card-img">
                </div>
                <div class="producto-card-body">
                    <h3 class="producto-card-titulo" style="color: #0891b2">Inmunidad Avanzada</h3>
                    <div style="flex: 1">
                        <p class="producto-card-intro">Apoya tu complementación diaria con la nanotecnología exclusiva de 4Life.</p>
                        <ul class="producto-card-lista">
                            <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg><span>Moléculas de transferencia de inmunidad</span></li>
                            <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg><span>Refuerzo de células N y K</span></li>
                            <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg><span>Cápsulas vegetales</span></li>
                        </ul>
                    </div>
                    <div class="fx gap8">
                        <button class="btn-primary" style="flex: 1; font-size: 13px; padding: 12px 6px" onclick="abrirTienda()">Comprar</button>
                        <button class="btn-secondary" style="flex: 1; font-size: 13px; padding: 12px 6px" data-producto="Inmunidad Avanzada" onclick="abrirWhatsappProducto(this)">Más info</button>
                    </div>
                </div>
            </div>

            <div class="producto-card">
                <div class="producto-card-img-wrap">
                    <img src="{{ asset('images/product-riovida.png') }}" alt="4Life RioVida" class="producto-card-img">
                </div>
                <div class="producto-card-body">
                    <h3 class="producto-card-titulo" style="color: #10b981">Energía &amp; Vitalidad</h3>
                    <div style="flex: 1">
                        <p class="producto-card-intro">4Life RioVida™ con antioxidantes naturales para energía sostenida.</p>
                        <ul class="producto-card-lista">
                            <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg><span>Jugo de granada + acai</span></li>
                            <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg><span>Antioxidantes naturales</span></li>
                            <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg><span>Vitalidad</span></li>
                        </ul>
                    </div>
                    <div class="fx gap8">
                        <button class="btn-primary" style="flex: 1; font-size: 13px; padding: 12px 6px" onclick="abrirTienda()">Comprar</button>
                        <button class="btn-secondary" style="flex: 1; font-size: 13px; padding: 12px 6px" data-producto="Energía &amp; Vitalidad" onclick="abrirWhatsappProducto(this)">Más info</button>
                    </div>
                </div>
            </div>

            <div class="producto-card">
                <div class="producto-card-img-wrap">
                    <img src="{{ asset('images/product-tf-ag-pro.png') }}" alt="4Life TF AG Pro" class="producto-card-img">
                </div>
                <div class="producto-card-body">
                    <h3 class="producto-card-titulo" style="color: #84cc16">Envejecimiento Saludable</h3>
                    <div style="flex: 1">
                        <p class="producto-card-intro">Ayuda a mantener las células más jóvenes y funcionales.</p>
                        <ul class="producto-card-lista">
                            <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg><span>Aumenta la energía celular NAD+</span></li>
                            <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg><span>Componentes naturales</span></li>
                            <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg><span>Elimina células zombie</span></li>
                        </ul>
                    </div>
                    <div class="fx gap8">
                        <button class="btn-primary" style="flex: 1; font-size: 13px; padding: 12px 6px" onclick="abrirTienda()">Comprar</button>
                        <button class="btn-secondary" style="flex: 1; font-size: 13px; padding: 12px 6px" data-producto="Envejecimiento Saludable" onclick="abrirWhatsappProducto(this)">Más info</button>
                    </div>
                </div>
            </div>
        @endif
        </div>
    </div>
</section>

{{-- ── ¿POR QUÉ ELEGIR 4LIFE? ───────────────────────────────────────── --}}
<section class="p48" style="padding: 80px 20px; background: linear-gradient(180deg, #f9fafb, #f3f4f6)">
    <div class="container">
        <h2 class="section-title">¿Por qué elegir 4Life?</h2>
        <p class="section-subtitle">Más de 28 años transformando vidas con ciencia y naturaleza</p>
        <div class="fx gap24 wrap">
            <div style="flex: 1; min-width: 200px; text-align: center">
                <div style="font: 800 32px 'Poppins'; color: #0891b2; margin-bottom: 8px">28+</div>
                <p class="fw6" style="margin-bottom: 8px">Años de Investigación</p>
                <p style="color: #6b7280; font-size: 13px">Décadas de experiencia en bienestar científico</p>
            </div>
            <div style="flex: 1; min-width: 200px; text-align: center">
                <div style="font: 800 32px 'Poppins'; color: #10b981; margin-bottom: 8px">100%</div>
                <p class="fw6" style="margin-bottom: 8px">Ingredientes Naturales</p>
                <p style="color: #6b7280; font-size: 13px">Sin aditivos sintéticos ni rellenos innecesarios</p>
            </div>
            <div style="flex: 1; min-width: 200px; text-align: center">
                <div style="font: 800 32px 'Poppins'; color: #84cc16; margin-bottom: 8px">5M+</div>
                <p class="fw6" style="margin-bottom: 8px">Clientes Satisfechos</p>
                <p style="color: #6b7280; font-size: 13px">Comunidad global de bienestar</p>
            </div>
            <div style="flex: 1; min-width: 200px; text-align: center">
                <div style="color: #be185d; margin-bottom: 8px">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="fw6" style="margin-bottom: 8px">Garantía 100%</p>
                <p style="color: #6b7280; font-size: 13px">Satisfacción garantizada o tu dinero de vuelta</p>
            </div>
        </div>
    </div>
</section>

{{-- ── UBICACIÓN ─────────────────────────────────────────────────────── --}}
<section class="p48" style="padding: 80px 20px; background: #fff">
    <div class="container">
        <h2 class="section-title" style="display: flex; align-items: center; gap: 12px">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="1.5" style="flex-shrink: 0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
            </svg>
            Nuestra Ubicación
        </h2>
        <p class="section-subtitle">Estamos aquí para servirte</p>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; align-items: start" class="ubicacion-grid">
            <div>
                <div id="map"></div>
                <div id="map-fallback" style="display: none; height: 400px; border-radius: 12px; background: #f0f9ff; align-items: center; justify-content: center; text-align: center; padding: 24px; color: #6b7280; font-size: 14px">
                    Aún no tenemos un punto físico registrado en tu país.
                </div>
            </div>
            <div>
                <h3 class="fs24 fw7 mb16" style="color: #0891b2">Visítanos o Contáctanos</h3>
                <p style="color: #6b7280; margin-bottom: 24px; line-height: 1.8">Estamos aquí para ayudarte con todas tus preguntas sobre bienestar integral con productos 4Life.</p>

                <div id="ubicacion-sin-datos" style="display: none; padding: 16px; background: #fef9c3; border-radius: 8px; color: #854d0e; font-size: 14px; margin-bottom: 32px">
                    Aún no tenemos información de contacto registrada para tu país — cambia de país arriba a la derecha o vuelve pronto.
                </div>

                <div id="ubicacion-info" style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 32px">
                    <div id="bloque-direccion" style="padding: 16px; background: #f0f9ff; border-radius: 8px; border-left: 4px solid #0891b2">
                        <p style="color: #6b7280; font-size: 14px">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="2" style="display: inline-block; vertical-align: -2px"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            <strong style="color: #0891b2">Ubicación:</strong> <span data-field="direccion"></span></p>
                    </div>
                    <div id="bloque-whatsapp" style="padding: 16px; background: #f0f9ff; border-radius: 8px; border-left: 4px solid #0891b2">
                        <p style="color: #6b7280; font-size: 14px">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="2" style="display: inline-block; vertical-align: -2px"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 10.5h7.5m-7.5 3h4.5m4.5-9H5.25A2.25 2.25 0 003 6.75v7.5A2.25 2.25 0 005.25 16.5H9l3 3 3-3h3.75A2.25 2.25 0 0021 14.25v-7.5A2.25 2.25 0 0018.75 4.5z"/></svg>
                            <strong style="color: #0891b2">WhatsApp:</strong> <a href="#" onclick="abrirWhatsapp('Hola, me interesa saber más sobre 4Life'); return false;" style="color: #0891b2; font-weight: 600; text-decoration: underline">Contacta aquí</a></p>
                    </div>
                    <div style="padding: 16px; background: #f0f9ff; border-radius: 8px; border-left: 4px solid #0891b2">
                        <p style="color: #6b7280; font-size: 14px">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="2" style="display: inline-block; vertical-align: -2px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9" stroke-linecap="round"/></svg>
                            <strong style="color: #0891b2">Disponibilidad:</strong> 24/7</p>
                    </div>
                </div>
                <button class="btn-primary" onclick="abrirWhatsapp('Hola, quiero saber más sobre bienestar 4Life')">Enviar Mensaje WhatsApp</button>
            </div>
        </div>
    </div>
</section>

{{-- ── CTA FINAL ─────────────────────────────────────────────────────── --}}
<section class="p48" style="padding: 80px 20px; background: linear-gradient(135deg, #06b6d4 0%, #10b981 50%, #84cc16 100%); color: #fff">
    <div class="container text-center">
        <h2 class="fs40 fw8" style="margin-bottom: 24px; color: #fff">Comienza Tu Transformación Hoy</h2>
        <p class="fs18" style="margin-bottom: 40px; opacity: .95">Únete a miles de personas que ya están viviendo su mejor versión con 4Life</p>
        <div class="fx gap16 jc wrap">
            <button class="btn-primary" onclick="abrirTienda()" style="background: #fff; color: #0891b2; border: none">Ir a Shop 4Life →</button>
            <button class="btn-secondary" style="border-color: #fff; color: #fff" onclick="abrirWhatsapp('Hola, me gustaría saber más sobre los planes de 4Life bienestar')">Hablar por WhatsApp →</button>
        </div>
    </div>
</section>

{{-- ── FOOTER ────────────────────────────────────────────────────────── --}}
<footer class="p48" style="padding: 60px 20px; background: #1f2937; color: #fff">
    <div class="container">
        <div class="fx gap32 wrap mb48">
            <div style="flex: 1; min-width: 220px">
                <h4 class="fw6 mb16">EIRA - Bienestar 4Life</h4>
                <p style="color: #d1d5db; font-size: 14px; line-height: 1.8">Productos de bienestar respaldados por ciencia. Transforma tu salud, transforma tu vida.</p>
            </div>
            <div style="flex: 1; min-width: 220px">
                <h4 class="fw6 mb16">Contacto</h4>
                <p id="footer-direccion" style="display: none; color: #d1d5db; font-size: 14px; margin-bottom: 8px">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" style="display: inline-block; vertical-align: -2px"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                    Ubicación: <span data-field="direccion-corta"></span></p>
                <p id="footer-whatsapp" style="display: none; color: #d1d5db; font-size: 14px; margin-bottom: 8px">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" style="display: inline-block; vertical-align: -2px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h1.5a1.5 1.5 0 001.5-1.5v-2.379a1 1 0 00-.804-.98l-4.111-.822a1 1 0 00-.98.29l-.877.878a1.5 1.5 0 01-1.591.358c-1.612-.61-3.32-2.316-3.929-3.928a1.5 1.5 0 01.357-1.591l.878-.877a1 1 0 00.29-.98l-.822-4.11a1 1 0 00-.98-.805H3.75a1.5 1.5 0 00-1.5 1.5v.75z"/></svg>
                    WhatsApp: <a href="#" onclick="abrirWhatsapp('Hola, me interesa saber más sobre bienestar integral EIRA 4Life'); return false;" style="color: #0891b2; font-weight: 600">Chat aquí</a></p>
                <p id="footer-codigo" style="display: none; color: #d1d5db; font-size: 14px; margin-bottom: 8px">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" style="display: inline-block; vertical-align: -2px"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>
                    Código 4Life: <span data-field="codigo4life"></span></p>
                <p id="footer-tienda" style="display: none; color: #d1d5db; font-size: 14px">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" style="display: inline-block; vertical-align: -2px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M12 3c2.4 2.5 3.7 5.6 3.7 9s-1.3 6.5-3.7 9c-2.4-2.5-3.7-5.6-3.7-9s1.3-6.5 3.7-9z"/></svg>
                    <a href="#" onclick="abrirTienda(); return false;" data-field="tienda-link" style="color: #0891b2; font-weight: 600"></a></p>
            </div>
        </div>
        <div style="border-top: 1px solid #374151; padding-top: 24px; text-align: center">
            <p style="color: #9ca3af; font-size: 12.5px; line-height: 1.7; max-width: 640px; margin: 0 auto 12px">EIRA es un negocio de distribución independiente de 4Life. No somos 4Life Research LC ni una entidad oficial de la marca — los productos y logotipos mencionados son propiedad de sus respectivos dueños.</p>
            <p style="color: #9ca3af; font-size: 13px">© {{ date('Y') }} EIRA - Bienestar 4Life. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<style>
    @media (max-width: 900px) {
        .ubicacion-grid { grid-template-columns: 1fr !important; }
    }
</style>

<script>
    const REGIONS = {!! $regionesJson !!};
    const PAIS_DETECTADO_POR_IP = {!! json_encode($ipDetectadaSlug) !!};

    let currentRegionSlug = null;
    let mapInstance = null;
    let mapMarker = null;

    document.addEventListener('DOMContentLoaded', function () {
        // 1) Si el visitante ELIGIÓ su país a mano alguna vez (clic en una
        //    bandera, o en "Cambiar país"), eso manda siempre — nunca se
        //    pisa solo, ni aunque cambie de IP/red en otra visita.
        const guardado = localStorage.getItem('region_4life');
        const fueManual = localStorage.getItem('region_4life_manual') === '1';
        if (guardado && REGIONS[guardado] && fueManual) {
            aplicarRegion(guardado);
            return;
        }

        // 2) País detectado por el servidor a partir de la IP EN ESTA VISITA
        //    (respeta VPN/proxy, no pide permiso, ya viene resuelto en el
        //    HTML). A propósito NO se guarda como "manual" — así, si la
        //    próxima visita viene de otra IP/país, se vuelve a actualizar
        //    solo en vez de quedarse pegado al primero que detectó.
        if (PAIS_DETECTADO_POR_IP && REGIONS[PAIS_DETECTADO_POR_IP]) {
            aplicarRegion(PAIS_DETECTADO_POR_IP);
            localStorage.setItem('region_4life', PAIS_DETECTADO_POR_IP);
            return;
        }

        // 3) No se pudo detectar por IP esta vez (ej. red local) — si hubo
        //    algo detectado automáticamente en una visita anterior, úsalo
        //    mientras tanto en lugar de preguntar de nuevo.
        if (guardado && REGIONS[guardado]) {
            aplicarRegion(guardado);
            return;
        }

        // 4) Nada de nada — que elija a mano.
        mostrarSelectorRegion();
    });

    function mostrarSelectorRegion() {
        document.getElementById('region-overlay').style.display = 'flex';
    }

    /** Se llama SOLO cuando el visitante elige a mano (clic en una bandera) — esa elección ya no se toca sola. */
    function seleccionarRegion(slug) {
        if (!REGIONS[slug]) return;
        localStorage.setItem('region_4life', slug);
        localStorage.setItem('region_4life_manual', '1');
        aplicarRegion(slug);
        document.getElementById('region-overlay').style.display = 'none';
    }

    function regionActual() {
        return REGIONS[currentRegionSlug] || null;
    }

    function abrirWhatsapp(mensaje) {
        const r = regionActual();
        if (!r || !r.whatsapp) {
            alert('Aún no tenemos WhatsApp configurado para tu país. Cambia de país arriba a la derecha o vuelve pronto.');
            return;
        }
        // r.whatsapp ya viene resuelto del servidor: es el agente "de turno"
        // ahora mismo (rota cada 10 min) — el mismo que recibe el clic de
        // "Comprar" (r.tiendaUrl), para que la venta quede en su tienda.
        const url = 'https://wa.me/' + r.whatsapp + (mensaje ? ('?text=' + encodeURIComponent(mensaje)) : '');
        window.open(url, '_blank');
    }

    /** Botón "Más info" de una tarjeta de producto — arma el mensaje con el nombre del producto. */
    function abrirWhatsappProducto(boton) {
        const nombre = boton.dataset.producto || 'este producto';
        abrirWhatsapp('Hola, quiero más información sobre ' + nombre);
    }

    function abrirTienda() {
        const r = regionActual();
        if (!r || !r.tiendaUrl) {
            alert('Aún no tenemos tienda en línea configurada para tu país. Contáctanos por WhatsApp.');
            return;
        }
        window.open(r.tiendaUrl, '_blank');
    }

    function aplicarRegion(slug) {
        const r = REGIONS[slug];
        if (!r) return;
        currentRegionSlug = slug;

        // Pill "cambiar país"
        const pillFlag = document.getElementById('region-pill-flag');
        pillFlag.src = '{{ asset('images/flags') }}/' + slug + '.png';
        pillFlag.alt = r.nombre;
        document.getElementById('region-pill-nombre').textContent = r.nombre;
        document.getElementById('region-pill').style.display = 'inline-flex';

        // Ubicación: dirección y WhatsApp
        const tieneDireccion = !!r.direccion;
        const tieneWhatsapp  = !!r.whatsapp;
        const sinContacto    = !tieneDireccion && !tieneWhatsapp;

        document.getElementById('ubicacion-info').style.display = sinContacto ? 'none' : 'flex';
        document.getElementById('ubicacion-sin-datos').style.display = sinContacto ? 'block' : 'none';
        document.getElementById('bloque-direccion').style.display = tieneDireccion ? 'block' : 'none';
        document.getElementById('bloque-whatsapp').style.display = tieneWhatsapp ? 'block' : 'none';
        if (tieneDireccion) document.querySelector('#bloque-direccion [data-field="direccion"]').textContent = r.direccion;

        // Footer
        document.getElementById('footer-direccion').style.display = r.direccionCorta ? 'block' : 'none';
        if (r.direccionCorta) document.querySelector('#footer-direccion [data-field="direccion-corta"]').textContent = r.direccionCorta;

        document.getElementById('footer-whatsapp').style.display = tieneWhatsapp ? 'block' : 'none';

        document.getElementById('footer-codigo').style.display = r.codigo4life ? 'block' : 'none';
        if (r.codigo4life) document.querySelector('#footer-codigo [data-field="codigo4life"]').textContent = r.codigo4life;

        document.getElementById('footer-tienda').style.display = r.tiendaUrl ? 'block' : 'none';
        if (r.tiendaUrl) {
            const link = document.querySelector('[data-field="tienda-link"]');
            try { link.textContent = new URL(r.tiendaUrl).hostname; } catch (e) { link.textContent = r.tiendaUrl; }
        }

        actualizarMapa(r);
        filtrarProductosPorRegion(slug);
    }

    function actualizarMapa(r) {
        const mapEl = document.getElementById('map');
        const fallbackEl = document.getElementById('map-fallback');

        if (r.lat == null || r.lng == null) {
            mapEl.style.display = 'none';
            fallbackEl.style.display = 'flex';
            return;
        }

        mapEl.style.display = 'block';
        fallbackEl.style.display = 'none';

        if (!mapInstance) {
            setTimeout(function () {
                mapInstance = L.map('map', { scrollWheelZoom: false }).setView([r.lat, r.lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(mapInstance);
                mapMarker = L.marker([r.lat, r.lng]).addTo(mapInstance)
                    .bindPopup('<b style="font-size:14px">' + r.nombre + '</b>' + (r.direccion ? '<br/>' + r.direccion : ''))
                    .openPopup();
            }, 300);
        } else {
            mapInstance.setView([r.lat, r.lng], 15);
            mapMarker.setLatLng([r.lat, r.lng]);
            mapMarker.bindPopup('<b style="font-size:14px">' + r.nombre + '</b>' + (r.direccion ? '<br/>' + r.direccion : ''));
            setTimeout(function () { mapInstance.invalidateSize(); }, 200);
        }
    }

    function filtrarProductosPorRegion(slug) {
        const tarjetas = document.querySelectorAll('.producto-region-card');
        if (! tarjetas.length) return; // solo hay tarjetas fijas de fallback (sin catálogo sincronizado aún)

        let visibles = 0;
        tarjetas.forEach(function (card) {
            const regiones = (card.dataset.regions || '').split(',');
            const mostrar = regiones.includes(slug);
            card.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        const mensaje = document.getElementById('sin-productos-region');
        if (mensaje) mensaje.style.display = visibles === 0 ? 'block' : 'none';
    }
</script>

</body>
</html>
