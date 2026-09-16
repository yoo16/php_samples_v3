<?php
function businessCardText($value)
{
  return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function businessCardColor($value, $fallback)
{
  $value = (string) ($value ?? '');
  return preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? $value : $fallback;
}

function businessCardNumber($value, $fallback, $min, $max)
{
  $value = filter_var($value, FILTER_VALIDATE_INT);
  if ($value === false) {
    return $fallback;
  }
  return max($min, min($max, $value));
}

$color_title = businessCardColor($color_title ?? null, '#0f766e');
$color_name = businessCardColor($color_name ?? null, '#172033');
$color_info = businessCardColor($color_info ?? null, '#475569');
$bg_zoom = businessCardNumber($bg_zoom ?? null, 118, 40, 260);
$bg_x = businessCardNumber($bg_x ?? null, 50, 0, 100);
$bg_y = businessCardNumber($bg_y ?? null, 50, 0, 100);
$bg_base64 = (string) ($bg_base64 ?? '');

$bgStyle = "background-size: {$bg_zoom}%; background-position: {$bg_x}% {$bg_y}%;";
if (preg_match('/^data:image\/(?:png|jpe?g|gif|webp);base64,[a-zA-Z0-9+\/=\s]+$/', $bg_base64)) {
  $bgStyle .= " background-image: url('" . htmlspecialchars($bg_base64, ENT_QUOTES, 'UTF-8') . "');";
}
?>
<div class="card" style="<?= $bgStyle ?>">
  <table class="card-table" role="presentation">
    <tr>
      <td class="card-accent" rowspan="2"></td>
      <td class="identity">
        <div class="name" style="color: <?= $color_name ?>"><?= businessCardText($name ?? '') ?></div>
        <div class="title" style="color: <?= $color_title ?>"><?= businessCardText($title ?? '') ?></div>
      </td>
      <td class="brand-cell">
        <div class="brand-mark">BC</div>
      </td>
    </tr>
    <tr>
      <td class="info" colspan="2" style="color: <?= $color_info ?>">
        <div><span>Email</span><?= businessCardText($email ?? '') ?></div>
        <div><span>Web</span><?= businessCardText($web ?? '') ?></div>
        <div><span>Tel</span><?= businessCardText($tel ?? '') ?></div>
      </td>
    </tr>
  </table>
</div>
