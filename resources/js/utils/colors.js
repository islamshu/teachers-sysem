export function hexToRgb(hex) {
  const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex)
  return result
    ? { r: parseInt(result[1], 16), g: parseInt(result[2], 16), b: parseInt(result[3], 16) }
    : { r: 20, g: 184, b: 166 }
}

export function rgbToHex(r, g, b) {
  return '#' + [r, g, b].map(x => x.toString(16).padStart(2, '0')).join('')
}

function clamp(val) {
  return Math.min(255, Math.max(0, Math.round(val)))
}

function mix(color, weight, base = { r: 255, g: 255, b: 255 }) {
  return {
    r: clamp(base.r + (color.r - base.r) * weight),
    g: clamp(base.g + (color.g - base.g) * weight),
    b: clamp(base.b + (color.b - base.b) * weight),
  }
}

export function generatePrimaryShades(hex) {
  const c = hexToRgb(hex)
  return {
    50: mix(c, 0.95),
    100: mix(c, 0.85),
    200: mix(c, 0.65),
    300: mix(c, 0.45),
    400: mix(c, 0.25),
    500: c,
    600: mix(c, 0.8, { r: 0, g: 0, b: 0 }),
    700: mix(c, 0.6, { r: 0, g: 0, b: 0 }),
    800: mix(c, 0.4, { r: 0, g: 0, b: 0 }),
    900: mix(c, 0.2, { r: 0, g: 0, b: 0 }),
  }
}

export function applyPrimaryColor(hex) {
  const shades = generatePrimaryShades(hex)
  const root = document.documentElement
  Object.entries(shades).forEach(([key, rgb]) => {
    root.style.setProperty(`--color-primary-${key}`, `${rgb.r} ${rgb.g} ${rgb.b}`)
  })
}
