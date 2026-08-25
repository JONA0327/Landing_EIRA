<!-- debug-region: ip_detectada={{ request()->ip() }} pais_detectado={{ $ipDetectadaSlug ?? 'null' }} -->
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
        <div style="font-size: 40px; margin-bottom: 12px">🌎</div>
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

        <p style="color: #9ca3af; font-size: 11px; margin-top: 20px; line-height: 1.5; text-align: left">
            🔒 Detectamos tu país automáticamente a partir de tu conexión a internet, solo para mostrarte la atención, productos y contacto correspondientes a tu región — no la guardamos ni la compartimos con nadie. Si no es el país correcto, elígelo tú mismo arriba.
        </p>
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
        <h2 class="section-title">🎯 Categorías Destacadas</h2>
        <p class="section-subtitle">Soluciones completas para cada aspecto de tu bienestar</p>

        <div class="fx gap24 wrap" style="margin-bottom: 32px">
        @if ($productosDinamicos->isNotEmpty())
            {{-- Productos activados desde el panel admin (sincronizados del catálogo del CRM) --}}
            @foreach ($productosDinamicos as $producto)
                <div class="card p32 producto-region-card" data-regions="{{ $producto->regions->pluck('slug')->implode(',') }}"
                     style="flex: 1; min-width: 280px; cursor: pointer; border: 2px solid #f0f0f0"
                     onmouseover="this.style.borderColor='#0891b2';this.style.background='#f0f9ff'"
                     onmouseout="this.style.borderColor='#f0f0f0';this.style.background='#fff'">
                    <div class="text-center">
                        @if ($producto->imagen)
                            <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}" style="width: 100%; max-width: 200px; height: auto; margin-bottom: 16px; border-radius: 8px; object-fit: contain">
                        @endif
                        <h3 class="fs24 fw7" style="margin-bottom: 12px; color: #0891b2">{{ $producto->nombre }}</h3>
                        @if ($producto->descripcionLanding)
                            <div style="color: #6b7280; margin-bottom: 20px; text-align: left; white-space: pre-line">{{ $producto->descripcionLanding }}</div>
                        @endif
                        @if ($producto->precio)
                            <p style="color: #0891b2; font-weight: 700; margin-bottom: 20px">{{ $producto->precio }}</p>
                        @endif
                        <button class="btn-primary w100" style="font-size: 14px; padding: 12px">Ver Productos</button>
                    </div>
                </div>
            @endforeach
            <div id="sin-productos-region" style="display: none; width: 100%; text-align: center; padding: 40px 20px; color: #6b7280">
                <p style="font-size: 15px">Aún no tenemos catálogo cargado para tu país —
                    <a href="#" onclick="abrirWhatsapp('Hola, me interesa saber más sobre los productos 4Life disponibles en mi país'); return false;" style="color: #0891b2; font-weight: 600; text-decoration: underline">contáctanos por WhatsApp</a>.
                </p>
            </div>
        @else
            <div class="card p32" style="flex: 1; min-width: 280px; cursor: pointer; border: 2px solid #f0f0f0"
                 onmouseover="this.style.borderColor='#0891b2';this.style.background='#f0f9ff'"
                 onmouseout="this.style.borderColor='#f0f0f0';this.style.background='#fff'">
                <div class="text-center">
                    <img src="{{ asset('images/product-trifactor.png') }}" alt="4Life Trifactor" style="width: 100%; max-width: 200px; height: auto; margin-bottom: 16px; border-radius: 8px; object-fit: contain">
                    <h3 class="fs24 fw7" style="margin-bottom: 12px; color: #0891b2">Inmunidad Avanzada</h3>
                    <p style="color: #6b7280; margin-bottom: 20px">Apoya tu complementación diaria con la nanotecnología exclusiva de 4Life</p>
                    <ul style="text-align: left; list-style: none; margin-bottom: 20px">
                        <li style="padding: 8px 0; padding-left: 24px; position: relative"><span style="position: absolute; left: 0">✓</span>Moléculas de transferencia de inmunidad</li>
                        <li style="padding: 8px 0; padding-left: 24px; position: relative"><span style="position: absolute; left: 0">✓</span>Refuerzo de células N y K</li>
                        <li style="padding: 8px 0; padding-left: 24px; position: relative"><span style="position: absolute; left: 0">✓</span>Cápsulas Vegetales</li>
                    </ul>
                    <button class="btn-primary w100" style="font-size: 14px; padding: 12px">Ver Productos</button>
                </div>
            </div>

            <div class="card p32" style="flex: 1; min-width: 280px; cursor: pointer; border: 2px solid #f0f0f0"
                 onmouseover="this.style.borderColor='#10b981';this.style.background='#f0fdf4'"
                 onmouseout="this.style.borderColor='#f0f0f0';this.style.background='#fff'">
                <div class="text-center">
                    <img src="{{ asset('images/product-riovida.png') }}" alt="4Life RioVida" style="width: 100%; max-width: 200px; height: auto; margin-bottom: 16px; border-radius: 8px; object-fit: contain">
                    <h3 class="fs24 fw7" style="margin-bottom: 12px; color: #10b981">Energía &amp; Vitalidad</h3>
                    <p style="color: #6b7280; margin-bottom: 20px">4Life RioVida™ con antioxidantes naturales para energía sostenida</p>
                    <ul style="text-align: left; list-style: none; margin-bottom: 20px">
                        <li style="padding: 8px 0; padding-left: 24px; position: relative"><span style="position: absolute; left: 0">✓</span>Jugo de Granada + Acai</li>
                        <li style="padding: 8px 0; padding-left: 24px; position: relative"><span style="position: absolute; left: 0">✓</span>Antioxidantes Naturales</li>
                        <li style="padding: 8px 0; padding-left: 24px; position: relative"><span style="position: absolute; left: 0">✓</span>Vitalidad</li>
                    </ul>
                    <button class="btn-primary w100" style="font-size: 14px; padding: 12px">Ver Productos</button>
                </div>
            </div>

            <div class="card p32" style="flex: 1; min-width: 280px; cursor: pointer; border: 2px solid #f0f0f0"
                 onmouseover="this.style.borderColor='#84cc16';this.style.background='#fefce8'"
                 onmouseout="this.style.borderColor='#f0f0f0';this.style.background='#fff'">
                <div class="text-center">
                    <img src="{{ asset('images/product-tf-ag-pro.png') }}" alt="4Life TF AG Pro" style="width: 100%; max-width: 200px; height: auto; margin-bottom: 16px; border-radius: 8px; object-fit: contain">
                    <h3 class="fs24 fw7" style="margin-bottom: 12px; color: #84cc16">Envejecimiento Saludable</h3>
                    <p style="color: #6b7280; margin-bottom: 20px">Ayuda a mantener las células más jóvenes y funcionales</p>
                    <ul style="text-align: left; list-style: none; margin-bottom: 20px">
                        <li style="padding: 8px 0; padding-left: 24px; position: relative"><span style="position: absolute; left: 0">✓</span>Aumenta La Energía Célular NAD+</li>
                        <li style="padding: 8px 0; padding-left: 24px; position: relative"><span style="position: absolute; left: 0">✓</span>Componentes naturales</li>
                        <li style="padding: 8px 0; padding-left: 24px; position: relative"><span style="position: absolute; left: 0">✓</span>Eliminar Células Zombie</li>
                    </ul>
                    <button class="btn-primary w100" style="font-size: 14px; padding: 12px">Ver Productos</button>
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
                <div style="font: 800 32px 'Poppins'; color: #be185d; margin-bottom: 8px">✓</div>
                <p class="fw6" style="margin-bottom: 8px">Garantía 100%</p>
                <p style="color: #6b7280; font-size: 13px">Satisfacción garantizada o tu dinero de vuelta</p>
            </div>
        </div>
    </div>
</section>

{{-- ── UBICACIÓN ─────────────────────────────────────────────────────── --}}
<section class="p48" style="padding: 80px 20px; background: #fff">
    <div class="container">
        <h2 class="section-title">📍 Nuestra Ubicación</h2>
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
                        <p style="color: #6b7280; font-size: 14px">📍 <strong style="color: #0891b2">Ubicación:</strong> <span data-field="direccion"></span></p>
                    </div>
                    <div id="bloque-whatsapp" style="padding: 16px; background: #f0f9ff; border-radius: 8px; border-left: 4px solid #0891b2">
                        <p style="color: #6b7280; font-size: 14px">📱 <strong style="color: #0891b2">WhatsApp:</strong> <a href="#" onclick="abrirWhatsapp('Hola, me interesa saber más sobre 4Life'); return false;" style="color: #0891b2; font-weight: 600; text-decoration: underline">Contacta aquí</a></p>
                    </div>
                    <div style="padding: 16px; background: #f0f9ff; border-radius: 8px; border-left: 4px solid #0891b2">
                        <p style="color: #6b7280; font-size: 14px">⏰ <strong style="color: #0891b2">Disponibilidad:</strong> 24/7</p>
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
            <div style="flex: 1; min-width: 200px">
                <h4 class="fw6 mb16">EIRA - Bienestar 4Life</h4>
                <p style="color: #d1d5db; font-size: 14px; line-height: 1.8">Productos de bienestar respaldados por ciencia. Transforma tu salud, transforma tu vida.</p>
            </div>
            <div style="flex: 1; min-width: 200px">
                <h4 class="fw6 mb16">Contacto - EIRA</h4>
                <p style="color: #d1d5db; font-size: 14px; margin-bottom: 8px">👤 EIRA - Consultor 4Life</p>
                <p id="footer-direccion" style="display: none; color: #d1d5db; font-size: 14px; margin-bottom: 8px">📍 Ubicación: <span data-field="direccion-corta"></span></p>
                <p id="footer-whatsapp" style="display: none; color: #d1d5db; font-size: 14px; margin-bottom: 8px">📞 WhatsApp: <a href="#" onclick="abrirWhatsapp('Hola, me interesa saber más sobre bienestar integral EIRA 4Life'); return false;" style="color: #0891b2; font-weight: 600">Chat aquí</a></p>
                <p id="footer-codigo" style="display: none; color: #d1d5db; font-size: 14px; margin-bottom: 8px">🔑 Código 4Life: <span data-field="codigo4life"></span></p>
                <p id="footer-tienda" style="display: none; color: #d1d5db; font-size: 14px">🌐 <a href="#" onclick="abrirTienda(); return false;" data-field="tienda-link" style="color: #0891b2; font-weight: 600"></a></p>
            </div>
            <div style="flex: 1; min-width: 200px">
                <h4 class="fw6 mb16">Información</h4>
                <p style="color: #d1d5db; font-size: 14px; margin-bottom: 8px">✓ Garantía 100% satisfacción</p>
                <p style="color: #d1d5db; font-size: 14px; margin-bottom: 8px">✓ Envíos rápidos y seguros</p>
                <p style="color: #d1d5db; font-size: 14px">✓ Soporte 24/7</p>
            </div>
        </div>
        <div style="border-top: 1px solid #374151; padding-top: 24px; text-align: center">
            <p style="color: #9ca3af; font-size: 13px">© {{ date('Y') }} 4Life Research. Todos los derechos reservados. | <a href="#" style="color: #0891b2">Términos</a> · <a href="#" style="color: #0891b2">Privacidad</a></p>
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
        if (!r || !Array.isArray(r.whatsapp) || r.whatsapp.length === 0) {
            alert('Aún no tenemos WhatsApp configurado para tu país. Cambia de país arriba a la derecha o vuelve pronto.');
            return;
        }
        // Varios agentes de ventas por país — se reparte al azar entre ellos.
        const numero = r.whatsapp[Math.floor(Math.random() * r.whatsapp.length)];
        const url = 'https://wa.me/' + numero + (mensaje ? ('?text=' + encodeURIComponent(mensaje)) : '');
        window.open(url, '_blank');
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
        const tieneWhatsapp  = Array.isArray(r.whatsapp) && r.whatsapp.length > 0;
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
