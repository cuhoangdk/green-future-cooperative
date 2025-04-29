export const useParametersStore = defineStore('parameters', () => {
  const parameters = ref<Record<string, string>>({})
  const { getParameters } = useParameters()

  async function fetchParameters() {
    const { data } = await getParameters(['instagram', 'facebook', 'tiktok', 'phone_contact', 'address', 'email_contact'])
    const params = Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : []
    parameters.value = params.reduce((map, param) => {
      map[param.name] = param.value
      return map
    }, {} as Record<string, string>)
  }

  return { parameters, fetchParameters }
})