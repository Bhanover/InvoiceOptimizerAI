<?php /** InvoiceOptimizer Ai — Formulario de oferta unificado con arrays y bucles */ ?>

<!-- Campos obligatorios -->
<label class="form-label">Nombre de la oferta *</label>
<input type="text" name="nombre_oferta" class="form-input" required placeholder="ej. Tarifa Plana Plus">

<div class="form-grid-2">
    <div>
        <label class="form-label">Comercializadora *</label>
        <input type="text" name="comercializadora" class="form-input" required placeholder="ej. Iberdrola">
    </div>
    <div>
        <label class="form-label">Email comercializadora</label>
        <input type="email" name="email_comercializadora" class="form-input" placeholder="contratos@comercializadora.com">
    </div>
</div>

<?php
// Campos dinámicos para energías, potencias, fees y extras con placeholders específicos para los campos de texto
$campos = [
    // Energía P1-P6
    'precio_energia_p1' => 'Precio energía P1 (€/kWh)',
    'precio_energia_p2' => 'Precio energía P2 (€/kWh)',
    'precio_energia_p3' => 'Precio energía P3 (€/kWh)',
    'precio_energia_p4' => 'Precio energía P4 (€/kWh)',
    'precio_energia_p5' => 'Precio energía P5 (€/kWh)',
    'precio_energia_p6' => 'Precio energía P6 (€/kWh)',
    // Potencia P1-P6
    'precio_potencia_p1' => 'Precio potencia P1 (€/kW·día)',
    'precio_potencia_p2' => 'Precio potencia P2 (€/kW·día)',
    'precio_potencia_p3' => 'Precio potencia P3 (€/kW·día)',
    'precio_potencia_p4' => 'Precio potencia P4 (€/kW·día)',
    'precio_potencia_p5' => 'Precio potencia P5 (€/kW·día)',
    'precio_potencia_p6' => 'Precio potencia P6 (€/kW·día)',
    // Fees
    'fee_minimo' => 'Fee mínimo',
    'fee_maximo' => 'Fee máximo',
    // Extras con placeholder personalizado
    'version_tarifa' => [
        'label' => 'Versión tarifa',
        'placeholder' => 'ej. Tarifa Online 2026'
    ],
    'tipo_tarifa' => [
        'label' => 'Tipo de tarifa',
        'placeholder' => 'ej. Variable, Fija, Mixta'
    ]
];

// Campos ordenados para grid de 2 columnas
$campos_grid2 = [
    'precio_energia_p1','precio_energia_p2',
    'precio_energia_p3','precio_energia_p4',
    'precio_energia_p5','precio_energia_p6',
    'precio_potencia_p1','precio_potencia_p2',
    'precio_potencia_p3','precio_potencia_p4',
    'precio_potencia_p5','precio_potencia_p6',
    'fee_minimo','fee_maximo',
    'version_tarifa','tipo_tarifa'
];

// Renderizado de inputs respetando placeholders y tipos
foreach(array_chunk($campos_grid2, 2) as $pares){
    echo '<div class="form-grid-2">';
    foreach($pares as $name){
        // Si el campo es array, tiene label y placeholder, si no solo label
        if (is_array($campos[$name])) {
            $label = $campos[$name]['label'];
            $placeholder = ' placeholder="'.$campos[$name]['placeholder'].'"';
        } else {
            $label = $campos[$name];
            // Placeholder para numéricos
            $placeholder = (strpos($name,'precio')!==false || strpos($name,'fee')!==false) ? ' placeholder="0.00000"' : '';
        }
        // Tipo number para precios y fees, texto para el resto
        $type = (strpos($name,'precio')!==false || strpos($name,'fee')!==false) ? 'number' : 'text';
        $step = ($type==='number') ? ' step="0.00001"' : '';
        echo "<div>
            <label class=\"form-label\">$label</label>
            <input type=\"$type\" name=\"$name\" class=\"form-input\"$step$placeholder>
        </div>";
    }
    echo '</div>';
}
?>

<!-- Checkbox activa -->
<div style="display:flex;align-items:center;gap:8px;padding-top:12px">
    <input type="checkbox" name="activa" value="1" checked
           style="width:15px;height:15px;accent-color:var(--green);cursor:pointer">
    <label class="form-label" style="margin-bottom:0;cursor:pointer">Oferta activa</label>
</div>