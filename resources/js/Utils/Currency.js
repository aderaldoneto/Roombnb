export function formatCurrencyInput(value) {

  if (!value) return '0,00'

  const numeric = value.replace(/\D/g, '')

  if (numeric === '') return '0,00'

  const intValue = parseInt(numeric.slice(0, 11), 10)

  return (intValue / 100).toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}
