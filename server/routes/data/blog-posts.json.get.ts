import { readFileSync } from 'fs'
import { join } from 'path'

export default defineEventHandler(() => {
    // Lire le fichier JSON depuis public/api/
    const filePath = join(process.cwd(), 'public', 'api', 'blog-posts.json')
    const data = readFileSync(filePath, 'utf-8')
    return JSON.parse(data)
})
