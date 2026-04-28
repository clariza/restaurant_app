<?php if($client->birthdays && $client->birthdays->format('m-d') === now()->format('m-d')): ?>
    <?php $type = 'birthday'; ?>
<?php elseif($client->is_active): ?>
    <?php $type = 'premium'; ?>
<?php else: ?>
    <?php $type = null; ?>
<?php endif; ?>

<?php if($type): ?>
<div class="coupon-modal-overlay hidden" id="coupon-<?php echo e($client->id); ?>">
    <div class="coupon-modal-box">
        <button onclick="closeCoupon(<?php echo e($client->id); ?>)" class="coupon-close">&times;</button>

        <?php if($type === 'birthday'): ?>
        <div class="coupon-strip coupon-birthday">
        <?php else: ?>
        <div class="coupon-strip coupon-premium">
        <?php endif; ?>
            <!-- Lado izquierdo -->
            <div class="coupon-left">
                <?php if($type === 'birthday'): ?>
                    <span class="coupon-badge">🎂 Feliz Cumpleaños</span>
                    <div class="coupon-headline">BEBIDA<br>GRATIS</div>
                    <div class="coupon-sub">Un regalo especial en tu día.<br>Canjeable hoy en cualquier sucursal.</div>
                <?php else: ?>
                    <span class="coupon-badge">★ Cliente Premiado</span>
                    <div class="coupon-headline">20% OFF</div>
                    <div class="coupon-sub">En tu próxima visita al restaurante.<br>Presentar al momento de pagar.</div>
                <?php endif; ?>
                <div class="coupon-client"><?php echo e($client->full_name); ?></div>
                <div class="coupon-validity">
                    <?php if($type === 'birthday'): ?>
                        Solo válido hoy &nbsp;·&nbsp; *Aplican términos
                    <?php else: ?>
                        Válido hasta: <?php echo e(now()->addMonths(3)->format('d M Y')); ?> &nbsp;·&nbsp; *Aplican términos
                    <?php endif; ?>
                </div>
            </div>

            <!-- Separador dentado -->
            <div class="coupon-divider"></div>

            <!-- Lado derecho -->
            <div class="coupon-right">
                <?php if($type === 'birthday'): ?>
                    <div class="coupon-stamp">
                        <span style="font-size:22px">🎂</span>
                        <span class="coupon-stamp-off">GRATIS</span>
                    </div>
                <?php else: ?>
                    <div class="coupon-stamp">
                        <span class="coupon-stamp-pct">20%</span>
                        <span class="coupon-stamp-off">OFF</span>
                    </div>
                <?php endif; ?>
                <div>
                    <div class="coupon-code-label">Código</div>
                    <div class="coupon-code">
                        <?php echo e(strtoupper(substr($type === 'birthday' ? 'BDAY' : 'PREM', 0, 4))); ?>-<?php echo e(str_pad($client->id, 4, '0', STR_PAD_LEFT)); ?>

                    </div>
                </div>
                <div class="coupon-barcode">
                    <?php for($i = 0; $i < 18; $i++): ?>
                        <div class="coupon-bar" style="width:<?php echo e(rand(1,4)); ?>px; height:<?php echo e(rand(18,34)); ?>px"></div>
                    <?php endfor; ?>
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
<?php endif; ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/clients/partials/coupon.blade.php ENDPATH**/ ?>