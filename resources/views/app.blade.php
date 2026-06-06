<?php
  $siteName = \App\Models\Setting::get('site_name', config('app.name', 'Teachers'));
  $primaryColor = \App\Models\Setting::get('primary_color', '#10b981');
  $siteLogo = \App\Models\Setting::get('site_logo');

  function hexToRgb($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
  }

  function blend($c, $w, $b = [255, 255, 255]) {
    return [
      (int)round($b[0] + ($c[0] - $b[0]) * $w),
      (int)round($b[1] + ($c[1] - $b[1]) * $w),
      (int)round($b[2] + ($c[2] - $b[2]) * $w),
    ];
  }

  $c = hexToRgb($primaryColor);
  $weights = [
    50 => 0.95, 100 => 0.85, 200 => 0.65, 300 => 0.45, 400 => 0.25,
    500 => 1,
    600 => 0.8, 700 => 0.6, 800 => 0.4, 900 => 0.2,
  ];
  $vars = '';
  foreach ($weights as $shade => $w) {
    $rgb = $w === 1 ? $c : ($w > 0.5 ? blend($c, $w) : blend($c, $w, [0, 0, 0]));
    $vars .= "--color-primary-{$shade}: {$rgb[0]} {$rgb[1]} {$rgb[2]};";
  }
?><!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $siteName }}</title>

        @if($siteLogo)
        <link rel="icon" href="{{ asset('storage/' . $siteLogo) }}" />
        @endif

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="{{ $primaryColor }}" />
        <style>:root{ {{ $vars }} }</style>
        <script>if(top!==self){top.location=self.location}</script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=tajawal:400,500,700,800&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/js/app.js', 'resources/css/app.css'])
        @inertiaHead
    </head>
    <body class="font-sans">
        @inertia
    </body>
</html>
