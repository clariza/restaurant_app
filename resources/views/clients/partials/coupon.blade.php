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
                    <div class="coupon-sub">Un regalo especial en tu día.<br>Canjeable hoy en cualquier sucursal.</div>
                @else
                    <span class="coupon-badge">★ Cliente Premiado</span>
                    <div class="coupon-headline">20% OFF</div>
                    <div class="coupon-sub">En tu próxima visita al restaurante.<br>Presentar al momento de pagar.</div>
                @endif
                <div class="coupon-client">{{ $client->full_name }}</div>
                <div class="coupon-validity">
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
                        <span class="coupon-stamp-pct">20%</span>
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

        <!-- Botón imprimir -->
        <div style="text-align:center; margin-top:1rem;">
            <button onclick="window.print()" class="coupon-print-btn">
                🖨️ Imprimir cupón
            </button>
        </div>
    </div>
</div>
@endif