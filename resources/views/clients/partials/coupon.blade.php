@if($client->birthdays && $client->birthdays->format('m-d') === now()->format('m-d'))
    @php $type = 'birthday'; @endphp
@elseif($client->is_active)
    @php $type = 'premium'; @endphp
@else
    @php $type = null; @endphp
@endif

@if($type)
<div class="coupon-modal-overlay hidden" id="coupon-{{ $client->id }}">
    <div class="coupon-modal-box">
        <button onclick="closeCoupon({{ $client->id }})" class="coupon-close">&times;</button>

        {{-- ============================================================
             PANEL DE EDICIÓN (sólo visible antes de confirmar)
        ============================================================ --}}
        <div class="coupon-editor" id="coupon-editor-{{ $client->id }}">
            <h3 class="coupon-editor-title">
                ✏️ Personalizar cupón
                <span class="coupon-editor-subtitle">— {{ $client->full_name }}</span>
            </h3>

            <div class="coupon-editor-fields">
                @if($type === 'premium')
                {{-- Campo: porcentaje de descuento --}}
                <div class="coupon-field-group">
                    <label class="coupon-field-label">Descuento (%)</label>
                    <div class="coupon-field-row">
                        <input  type="number"
                                id="pct-{{ $client->id }}"
                                value="20"
                                min="1" max="100"
                                class="coupon-field-input"
                                oninput="updateCouponPreview({{ $client->id }})">
                        <span class="coupon-field-unit">%</span>
                    </div>
                </div>
                @endif

                {{-- Campo: mensaje de validez --}}
                <div class="coupon-field-group">
                    <label class="coupon-field-label">
                        @if($type === 'birthday') Texto de vigencia @else Fecha de vigencia @endif
                    </label>
                    @if($type === 'birthday')
                        <input  type="text"
                                id="validity-{{ $client->id }}"
                                value="Solo válido hoy · *Aplican términos"
                                class="coupon-field-input"
                                oninput="updateCouponPreview({{ $client->id }})">
                    @else
                        <input  type="date"
                                id="validity-{{ $client->id }}"
                                value="{{ now()->addMonths(3)->format('Y-m-d') }}"
                                class="coupon-field-input"
                                oninput="updateCouponPreview({{ $client->id }})">
                    @endif
                </div>

                {{-- Campo: sub-texto libre --}}
                <div class="coupon-field-group coupon-field-full">
                    <label class="coupon-field-label">Descripción del cupón</label>
                    <input  type="text"
                            id="sub-{{ $client->id }}"
                            value="@if($type === 'birthday')Un regalo especial en tu día. Canjeable hoy en cualquier sucursal.@else En tu próxima visita al restaurante. Presentar al momento de pagar.@endif"
                            class="coupon-field-input"
                            oninput="updateCouponPreview({{ $client->id }})">
                </div>
            </div>

            <button onclick="confirmCoupon({{ $client->id }})" class="coupon-confirm-btn">
                Ver cupón →
            </button>
        </div>

        {{-- ============================================================
             CUPÓN (oculto hasta confirmar)
        ============================================================ --}}
        <div class="hidden" id="coupon-preview-{{ $client->id }}">

            @if($type === 'birthday')
            <div class="coupon-strip coupon-birthday">
            @else
            <div class="coupon-strip coupon-premium">
            @endif

                <!-- Lado izquierdo -->
                <div class="coupon-left">
                    @if($type === 'birthday')
                        <span class="coupon-badge">🎂 Feliz Cumpleaños</span>
                        <div class="coupon-headline">BEBIDA<br>GRATIS</div>
                    @else
                        <span class="coupon-badge">★ Cliente Premiado</span>
                        {{-- El titular cambia dinámicamente --}}
                        <div class="coupon-headline" id="headline-{{ $client->id }}">20% OFF</div>
                    @endif

                    <div class="coupon-sub" id="coupon-sub-text-{{ $client->id }}">
                        @if($type === 'birthday')Un regalo especial en tu día.<br>Canjeable hoy en cualquier sucursal.
                        @else En tu próxima visita al restaurante.<br>Presentar al momento de pagar.
                        @endif
                    </div>

                    <div class="coupon-client">{{ $client->full_name }}</div>

                    <div class="coupon-validity" id="coupon-validity-text-{{ $client->id }}">
                        @if($type === 'birthday')
                            Solo válido hoy &nbsp;·&nbsp; *Aplican términos
                        @else
                            Válido hasta: {{ now()->addMonths(3)->format('d M Y') }} &nbsp;·&nbsp; *Aplican términos
                        @endif
                    </div>
                </div>

                <!-- Separador dentado -->
                <div class="coupon-divider"></div>

                <!-- Lado derecho -->
                <div class="coupon-right">
                    @if($type === 'birthday')
                        <div class="coupon-stamp">
                            <span style="font-size:22px">🎂</span>
                            <span class="coupon-stamp-off">GRATIS</span>
                        </div>
                    @else
                        <div class="coupon-stamp">
                            <span class="coupon-stamp-pct" id="stamp-pct-{{ $client->id }}">20%</span>
                            <span class="coupon-stamp-off">OFF</span>
                        </div>
                    @endif
                    <div>
                        <div class="coupon-code-label">Código</div>
                        <div class="coupon-code">
                            {{ strtoupper(substr($type === 'birthday' ? 'BDAY' : 'PREM', 0, 4)) }}-{{ str_pad($client->id, 4, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                    <div class="coupon-barcode">
                        @for($i = 0; $i < 18; $i++)
                            <div class="coupon-bar" style="width:{{ rand(1,4) }}px; height:{{ rand(18,34) }}px"></div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Acciones post-preview -->
            <div style="text-align:center; margin-top:1rem; display:flex; gap:.75rem; justify-content:center;">
                <button onclick="backToEditor({{ $client->id }})" class="coupon-back-btn">
                    ← Editar
                </button>
                <button onclick="downloadCoupon({{ $client->id }})"
        class="coupon-print-btn"
        id="download-btn-{{ $client->id }}">
    ⬇️ Descargar imagen
</button>
            </div>
        </div>

    </div>
</div>
@endif