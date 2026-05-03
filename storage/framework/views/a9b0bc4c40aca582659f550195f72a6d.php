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

        
        <div class="coupon-editor" id="coupon-editor-<?php echo e($client->id); ?>">
            <h3 class="coupon-editor-title">
                ✏️ Personalizar cupón
                <span class="coupon-editor-subtitle">— <?php echo e($client->full_name); ?></span>
            </h3>

            <div class="coupon-editor-fields">
                <?php if($type === 'premium'): ?>
                
                <div class="coupon-field-group">
                    <label class="coupon-field-label">Descuento (%)</label>
                    <div class="coupon-field-row">
                        <input  type="number"
                                id="pct-<?php echo e($client->id); ?>"
                                value="20"
                                min="1" max="100"
                                class="coupon-field-input"
                                oninput="updateCouponPreview(<?php echo e($client->id); ?>)">
                        <span class="coupon-field-unit">%</span>
                    </div>
                </div>
                <?php endif; ?>

                
                <div class="coupon-field-group">
                    <label class="coupon-field-label">
                        <?php if($type === 'birthday'): ?> Texto de vigencia <?php else: ?> Fecha de vigencia <?php endif; ?>
                    </label>
                    <?php if($type === 'birthday'): ?>
                        <input  type="text"
                                id="validity-<?php echo e($client->id); ?>"
                                value="Solo válido hoy · *Aplican términos"
                                class="coupon-field-input"
                                oninput="updateCouponPreview(<?php echo e($client->id); ?>)">
                    <?php else: ?>
                        <input  type="date"
                                id="validity-<?php echo e($client->id); ?>"
                                value="<?php echo e(now()->addMonths(3)->format('Y-m-d')); ?>"
                                class="coupon-field-input"
                                oninput="updateCouponPreview(<?php echo e($client->id); ?>)">
                    <?php endif; ?>
                </div>

                
                <div class="coupon-field-group coupon-field-full">
                    <label class="coupon-field-label">Descripción del cupón</label>
                    <input  type="text"
                            id="sub-<?php echo e($client->id); ?>"
                            value="<?php if($type === 'birthday'): ?>Un regalo especial en tu día. Canjeable hoy en cualquier sucursal.<?php else: ?> En tu próxima visita al restaurante. Presentar al momento de pagar.<?php endif; ?>"
                            class="coupon-field-input"
                            oninput="updateCouponPreview(<?php echo e($client->id); ?>)">
                </div>
            </div>

            <button onclick="confirmCoupon(<?php echo e($client->id); ?>)" class="coupon-confirm-btn">
                Ver cupón →
            </button>
        </div>

        
        <div class="hidden" id="coupon-preview-<?php echo e($client->id); ?>">

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
                    <?php else: ?>
                        <span class="coupon-badge">★ Cliente Premiado</span>
                        
                        <div class="coupon-headline" id="headline-<?php echo e($client->id); ?>">20% OFF</div>
                    <?php endif; ?>

                    <div class="coupon-sub" id="coupon-sub-text-<?php echo e($client->id); ?>">
                        <?php if($type === 'birthday'): ?>Un regalo especial en tu día.<br>Canjeable hoy en cualquier sucursal.
                        <?php else: ?> En tu próxima visita al restaurante.<br>Presentar al momento de pagar.
                        <?php endif; ?>
                    </div>

                    <div class="coupon-client"><?php echo e($client->full_name); ?></div>

                    <div class="coupon-validity" id="coupon-validity-text-<?php echo e($client->id); ?>">
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
                            <span class="coupon-stamp-pct" id="stamp-pct-<?php echo e($client->id); ?>">20%</span>
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

            <!-- Acciones post-preview -->
            <div style="text-align:center; margin-top:1rem; display:flex; gap:.75rem; justify-content:center;">
                <button onclick="backToEditor(<?php echo e($client->id); ?>)" class="coupon-back-btn">
                    ← Editar
                </button>
                <button onclick="downloadCoupon(<?php echo e($client->id); ?>)"
        class="coupon-print-btn"
        id="download-btn-<?php echo e($client->id); ?>">
    ⬇️ Descargar imagen
</button>
            </div>
        </div>

    </div>
</div>
<?php endif; ?><?php /**PATH C:\Users\HP\Desktop\laravel\repo\restaurant_app\resources\views/clients/partials/coupon.blade.php ENDPATH**/ ?>