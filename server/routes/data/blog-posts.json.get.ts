import { readFileSync } from 'fs'
import { join } from 'path'

export default defineEventHandler(() => {
    // Lire le fichier JSON depuis public/data/
    const filePath = join(process.cwd(), 'public', 'data', 'blog-posts.json')
    const data = readFileSync(filePath, 'utf-8')
    return JSON.parse(data)
})
