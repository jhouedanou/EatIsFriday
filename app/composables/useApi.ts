export const useApi = () => {
    const fetchData = async <T>(endpoint: string): Promise<T | null> => {
        try {
            // Use $fetch for static JSON files in public folder
            const data = await $fetch<T>(`/api/${endpoint}`)
            return data
        } catch (err) {
            console.error(`Failed to fetch ${endpoint}:`, err)
            return null
        }
    }

    return {
        fetchData
    }
}
