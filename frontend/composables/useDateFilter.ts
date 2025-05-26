export function useDateFilter() {
  const selectedYear = ref<string | null>('')
  const selectedMonth = ref<string | null>('')
  const selectedDay = ref<string | null>('')

  const years = computed(() => {
    const startYear = 2025
    const endYear = new Date().getFullYear()
    return Array.from({ length: endYear - startYear + 1 }, (_, i) => startYear + i)
  })

  const months = computed(() => {
    return Array.from({ length: 12 }, (_, i) => ({
      value: i + 1,
      display: String(i + 1).padStart(2, '0')
    }))
  })

  const days = computed(() => {
    if (!selectedYear.value || !selectedMonth.value) {
      return Array.from({ length: 31 }, (_, i) => ({
        value: i + 1,
        display: String(i + 1).padStart(2, '0')
      }))
    }
    const year = Number(selectedYear.value)
    const month = Number(selectedMonth.value)
    const maxDays = new Date(year, month, 0).getDate()
    return Array.from({ length: maxDays }, (_, i) => ({
      value: i + 1,
      display: String(i + 1).padStart(2, '0')
    }))
  })

  function onYearMonthChange() {
    if (selectedMonth.value && Number(selectedMonth.value) > 12) {
      selectedMonth.value = null
    }
    if (selectedDay.value) {
      const maxDays = days.value.length
      if (Number(selectedDay.value) > maxDays) {
        selectedDay.value = null
      }
    }
  }

  return {
    selectedYear,
    selectedMonth,
    selectedDay,
    years,
    months,
    days,
    onYearMonthChange
  }
}