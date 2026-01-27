# Eat Is Family WordPress Theme v2.0# Thème WordPress Eat Is Family



**Headless CMS Backend for Nuxt.js Application**Template WordPress personnalisé avec des endpoints REST API pour l'application Eat Is Family.



## 📋 Overview## 📋 Table des matières



This WordPress theme serves as the **backend/CMS** for the Eat Is Family Nuxt.js website. It provides:- [Installation](#installation)

- [Configuration](#configuration)

- ✅ **REST API endpoints** for all content types- [Endpoints API](#endpoints-api)

- ✅ **Admin interface** with WYSIWYG editors and meta boxes- [Custom Post Types](#custom-post-types)

- ✅ **No hardcoded values** - everything is editable via WordPress admin- [Import des données](#import-des-données)

- ✅ **No automatic JSON updates** - WordPress is the single source of truth- [Structure des données](#structure-des-données)

- ✅ **Complete content management** for Activities, Jobs, Venues, Events, Blog, and Pages

## 🚀 Installation

## 🎯 Key Features

### Prérequis

### Content Types (Custom Post Types)- WordPress 6.0 ou supérieur

1. **Activities** - Culinary activities and workshops- PHP 8.0 ou supérieur

2. **Events** - Company events and partnerships- MySQL 5.7+ ou MariaDB 10.3+

3. **Jobs** - Career opportunities and positions

4. **Venues** - Locations and stadiums### Étapes d'installation

5. **Blog Posts** - Articles and news

6. **Timeline Events** - Company history milestones1. **Télécharger le thème**

   ```bash

### Admin Pages   cd wp-content/themes/

1. **Site Content** - Global site settings (SEO, contact, social media)   git clone [url-du-repo] eatisfamily

2. **Pages Content** - Page-specific content (hero sections, CTAs, etc.)   ```

3. **Data Management** - Manual JSON import tools   Ou téléversez le dossier `wordpress-theme` et renommez-le en `eatisfamily`.



### REST API Endpoints2. **Activer le thème**

   - Connectez-vous à l'administration WordPress

All endpoints are available at: `https://yoursite.com/wp-json/eatisfamily/v1/`   - Allez dans `Apparence > Thèmes`

   - Activez le thème "Eat Is Family"

#### Activities

- `GET /activities` - List all activities3. **Configurer les permaliens**

- `GET /activities/{slug}` - Get single activity by slug   - Allez dans `Réglages > Permaliens`

   - Sélectionnez "Nom de l'article" ou une structure personnalisée

#### Blog Posts   - Enregistrez les modifications

- `GET /blog-posts` - List all blog posts

- `GET /blog-posts/{slug}` - Get single post by slug4. **Vérifier les endpoints**

   - Visitez : `https://votresite.com/wp-json/eatisfamily/v1/`

#### Events   - Vous devriez voir les routes disponibles

- `GET /events` - List all events

- `GET /events/{id}` - Get single event by ID## ⚙️ Configuration



#### Jobs### Activation des Custom Post Types

- `GET /jobs` - List all jobs (supports filters: `?department=X&venue_id=Y`)

- `GET /jobs/{slug}` - Get single job by slugLe thème enregistre automatiquement les Custom Post Types suivants :

- **Activities** (`activity`)

#### Venues- **Events** (`event`)

- `GET /venues` - List all venues with metadata- **Jobs** (`job`)

- `GET /venues/{id}` - Get single venue by ID- **Venues** (`venue`)



#### Site ContentCes CPT sont accessibles dans l'administration WordPress après activation du thème.

- `GET /site-content` - Get global site content (SEO, contact, social)

### Configuration CORS

#### Pages Content

- `GET /pages-content` - Get all pages content (hero sections, CTAs)Les headers CORS sont automatiquement ajoutés pour permettre les requêtes cross-origin. Pour restreindre l'accès, modifiez la fonction `eatisfamily_add_cors_headers()` dans `functions.php`.



## 🚀 Installation## 📡 Endpoints API



### PrerequisitesTous les endpoints sont disponibles sous le namespace `eatisfamily/v1`.

- WordPress 6.0+

- PHP 8.0+### Activities

- MySQL 5.7+ / MariaDB 10.3+

**Liste toutes les activités**

### Steps```

GET /wp-json/eatisfamily/v1/activities

1. **Upload Theme**```

   ```bash

   # Upload to /wp-content/themes/ or via WordPress admin**Récupérer une activité par slug**

   ``````

GET /wp-json/eatisfamily/v1/activities/{slug}

2. **Activate Theme**```

   - Go to Appearance > Themes

   - Activate "Eat Is Family"Exemple : `/wp-json/eatisfamily/v1/activities/cooking-workshop-italian-cuisine`



3. **Configure Permalinks**### Blog Posts

   - Go to Settings > Permalinks

   - Select "Post name"**Liste tous les articles**

   - Save changes```

GET /wp-json/eatisfamily/v1/blog-posts

4. **Import Initial Data (Optional)**```

   - Place JSON files in `/wp-content/themes/eatisfamily/data/`

   - Go to Site Content > Data Management**Récupérer un article par slug**

   - Select content types to import```

   - Click "Import Selected Data"GET /wp-json/eatisfamily/v1/blog-posts/{slug}

```

5. **Start Adding Content**

   - Use WordPress admin to create Activities, Jobs, Venues, etc.### Events

   - Edit Site Content and Pages Content via admin pages

**Liste tous les événements**

## 📝 Content Management```

GET /wp-json/eatisfamily/v1/events

### Creating Content```



#### Activities**Récupérer un événement par ID**

1. Go to **Activities > Add New**```

2. Fill in:GET /wp-json/eatisfamily/v1/events/{id}

   - Title (e.g., "Italian Cooking Workshop")```

   - Content (description)

   - Featured Image### Jobs

   - Meta fields: date, location, price, capacity, etc.

3. Publish**Liste toutes les offres d'emploi**

```

#### JobsGET /wp-json/eatisfamily/v1/jobs

1. Go to **Jobs > Add New**```

2. Fill in:

   - Title (e.g., "Head Chef – VIP Hospitality")Paramètres de filtrage disponibles :

   - Content (job description)- `department` : Filtrer par département

   - Featured Image- `venue_id` : Filtrer par lieu

   - Meta fields: venue, department, salary, requirements, benefits

   - Life at Venue gallery (optional)Exemple : `/wp-json/eatisfamily/v1/jobs?department=Culinary`

3. Publish

**Récupérer une offre par slug**

#### Venues```

1. Go to **Venues > Add New**GET /wp-json/eatisfamily/v1/jobs/{slug}

2. Fill in:```

   - Title (e.g., "Stade Jean Bouin")

   - Content (venue description)### Venues

   - Featured Image

   - Meta fields: location, coordinates, capacity, services, shops, menu items**Récupérer tous les lieux avec métadonnées**

3. Publish```

GET /wp-json/eatisfamily/v1/venues

#### Events```

1. Go to **Events > Add New**

2. Fill in:Retourne :

   - Title (e.g., "The Adidas Arena Partnership")- `metadata` : Titre, description, labels

   - Content (event description)- `event_types` : Types d'événements

   - Featured Image- `stats` : Statistiques du site

   - Meta fields: event type, related venue- `venues` : Liste des lieux

3. Publish

**Récupérer un lieu par ID**

#### Timeline Events (for About page)```

1. Go to **Timeline > Add New**GET /wp-json/eatisfamily/v1/venues/{id}

2. Fill in:```

   - Title (e.g., "Company Founded")

   - Event date (e.g., "13 JUNE 2015")### Site Content

   - Description

   - Featured Image**Récupérer le contenu global du site**

   - Display order```

3. PublishGET /wp-json/eatisfamily/v1/site-content

```

### Global Content

Contient :

#### Site Content- Informations du site

Go to **Site Content > Site Content** to edit:- Contact et réseaux sociaux

- Site name, tagline, description- SEO metadata

- SEO settings (default title, description, keywords, OG image)- Contenu des pages principales

- Contact information (email, phone)

- Social media links (Facebook, Instagram, Twitter, LinkedIn, YouTube)### Pages Content



#### Pages Content**Récupérer le contenu des pages**

Go to **Site Content > Pages Content** to edit:```

- **Homepage**: Hero section (title, subtitle, CTA, background image)GET /wp-json/eatisfamily/v1/pages-content

- **About**: Hero, intro section, timeline title```

- **Contact**: Hero, form titles

- **Careers**: Hero, benefits list## 📝 Custom Post Types

- **Events**: Hero section

### Activity

## 🔗 Nuxt.js Integration

**Champs personnalisés (Custom Fields) :**

Update your Nuxt.js `nuxt.config.ts`:- `description` : Description courte

- `activity_date` : Date de l'activité (format ISO 8601)

```typescript- `location` : Lieu

export default defineNuxtConfig({- `capacity` : Capacité totale (nombre)

  runtimeConfig: {- `available_spots` : Places disponibles (nombre)

    public: {- `category` : Catégorie

      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'https://wordpress.yoursite.com/wp-json/eatisfamily/v1',- `price` : Prix

      useLocalFallback: false // Always use WordPress API- `duration` : Durée

    }- `status` : Statut (open, closed, full)

  }

})### Event

```

**Champs personnalisés :**

Your existing composables (`useActivities`, `useJobs`, `useVenues`, etc.) will work without changes!- `image` : URL de l'image (si différente de l'image mise en avant)

- `event_type` : Type d'événement

## 🛡️ Data Flow (v2.0)- `event_order` : Ordre d'affichage (nombre)



```### Job

WordPress Admin (Edit Content)

        ↓**Champs personnalisés :**

WordPress Database- `venue_id` : Identifiant du lieu

        ↓- `department` : Département

REST API Endpoints- `job_type` : Type de contrat

        ↓- `salary` : Salaire

Nuxt.js Application (Frontend)- `requirements` : Exigences (JSON array)

```- `benefits` : Avantages (JSON array)



### Important NotesExemple de `requirements` (stocker en JSON) :

- ⚠️ **WordPress is the single source of truth** - all content is stored in WordPress database```json

- ⚠️ **JSON files are NOT automatically updated** - they're only used for initial import["5+ years experience", "Strong leadership skills", "HACCP certification"]

- ⚠️ **Always edit content via WordPress admin** - not via JSON files```

- ⚠️ **Nuxt.js fetches data from WordPress API** - not from local JSON files

### Venue

## 🔧 Customization

**Champs personnalisés :**

### Adding Custom Fields- `location` : Adresse complète

Edit `/inc/meta-boxes.php` to add new fields to existing post types.- `city` : Ville

- `country` : Pays

### Adding New Endpoints- `venue_type` : Type (stadium, festival, arena)

Edit `/functions.php` in the "REST API ROUTES" section:- `latitude` : Latitude (float)

- `longitude` : Longitude (float)

```php- `capacity` : Capacité (nombre)

register_rest_route('eatisfamily/v1', '/your-endpoint', array(- `amenities` : Équipements (JSON array)

    'methods' => 'GET',

    'callback' => 'your_callback_function',## 📊 Import des données

    'permission_callback' => '__return_true',

));### Méthode 1 : Via l'administration WordPress

```

1. Créez manuellement les posts dans chaque Custom Post Type

### Modifying Data Format2. Remplissez les champs personnalisés via l'interface

Edit the format functions in `/functions.php`:3. Ajoutez les images mises en avant

- `eatisfamily_format_activity()`

- `eatisfamily_format_job()`### Méthode 2 : Via plugin d'import

- `eatisfamily_format_venue()`

- etc.Utilisez un plugin comme **WP All Import** ou **Advanced Custom Fields** pour importer les données depuis vos fichiers JSON.



## 📊 Meta Boxes Features### Méthode 3 : Script PHP personnalisé



All meta boxes include:Un fichier `import-data.php` est inclus dans le dossier du thème. Voir ci-dessous.

- **WYSIWYG editors** for rich text content

- **Dynamic dropdowns** for relationships (venues, departments, categories)### Utilisation du script d'import

- **Repeater fields** for lists (requirements, benefits, services)

- **Media upload** buttons integrated with WordPress media library1. Placez vos fichiers JSON dans `wp-content/uploads/import/`

- **Gallery fields** for multiple images2. Accédez à : `https://votresite.com/?import_eatisfamily_data=1`

- **Complex repeaters** for structured data (shops, menu items)3. Les données seront importées automatiquement

4. **IMPORTANT** : Supprimez ou commentez le code d'import après utilisation pour des raisons de sécurité

## 🔍 Debugging

## 🔧 Options du thème

### Check API Responses

```bash### Configurer les métadonnées des venues

# Test endpoints

curl https://yoursite.com/wp-json/eatisfamily/v1/activitiesDans l'admin WordPress, utilisez un plugin comme **Advanced Custom Fields** ou ajoutez via le code :

curl https://yoursite.com/wp-json/eatisfamily/v1/jobs

curl https://yoursite.com/wp-json/eatisfamily/v1/venues```php

```update_option('eatisfamily_venues_metadata', array(

    'title' => 'Explore Where We Operate',

### Enable WordPress Debug    'description' => 'Description...',

```php    'filter_label' => 'Click to filter by an event type',

// In wp-config.php));

define('WP_DEBUG', true);```

define('WP_DEBUG_LOG', true);

define('WP_DEBUG_DISPLAY', false);### Configurer les types d'événements

```

```php

### Check Error Logsupdate_option('eatisfamily_event_types', array(

```bash    array('id' => 'stadium', 'name' => 'Stadium', 'image' => '/images/stadium.png'),

# Check WordPress debug log    array('id' => 'festival', 'name' => 'Festival', 'image' => '/images/festival.png'),

tail -f wp-content/debug.log));

``````



## 📦 Backup & Export### Configurer les statistiques



### Export Content```php

Use WordPress built-in export:update_option('eatisfamily_stats', array(

- Tools > Export    array('value' => '250+', 'label' => 'Food & Beverage Events in 2024'),

- Select content type    array('value' => '300,000', 'label' => 'People fed in 2024'),

- Download XML file));

```

### Database Backup

Use plugins like:### Configurer le contenu du site

- UpdraftPlus

- BackupBuddy```php

- All-in-One WP Migrationupdate_option('eatisfamily_site_content', array(

    'site' => array(

## 🔄 Version History        'name' => 'Eat Is Family',

        'tagline' => 'Celebrate Food',

### v2.0.0 (Current)        // ...

- ✅ Complete admin interface with meta boxes    ),

- ✅ WYSIWYG editors instead of JSON arrays));

- ✅ Dynamic dropdowns for relationships```

- ✅ Disabled automatic JSON import

- ✅ Added Site Content and Pages Content admin pages## 🔒 Sécurité

- ✅ Added Data Management page for manual import

- ✅ Added Timeline Events custom post type### Permissions API

- ✅ Removed all hardcoded values

- ✅ WordPress as single source of truthPar défaut, tous les endpoints sont publics (`permission_callback' => '__return_true'`). Pour ajouter de l'authentification :



### v1.0.0```php

- Initial release with basic REST APIregister_rest_route($namespace, '/activities', array(

- JSON-based data import    'methods' => 'GET',

- Basic meta boxes    'callback' => 'eatisfamily_get_activities',

    'permission_callback' => function() {

## 🤝 Support        return current_user_can('read');

    },

For issues or questions:));

- Check the WordPress admin "Data Management" page for status```

- Review REST API responses in browser

- Check WordPress debug logs### Rate Limiting

- Contact: hello@eatisfamily.fr

Considérez l'ajout d'un plugin de rate limiting pour protéger vos APIs contre les abus.

## 📄 License

## 🎨 Personnalisation

GPL-2.0-or-later

### Modifier le format de sortie

---

Éditez les fonctions `eatisfamily_format_*()` dans `functions.php` pour personnaliser la structure JSON retournée.

**Last Updated:** January 27, 2026  

**Version:** 2.0.0  ### Ajouter des endpoints

**Status:** Production Ready ✅

Exemple pour ajouter un endpoint personnalisé :

```php
register_rest_route('eatisfamily/v1', '/custom-endpoint', array(
    'methods' => 'GET',
    'callback' => 'your_callback_function',
    'permission_callback' => '__return_true',
));
```

## 📚 Structure des données

### Format Activity
```json
{
  "id": 1,
  "slug": "cooking-workshop",
  "title": {"rendered": "Cooking Workshop"},
  "description": "Short description",
  "content": {"rendered": "<p>Full HTML content</p>"},
  "date": "2026-01-15T18:00:00",
  "location": "Paris",
  "capacity": 12,
  "available_spots": 5,
  "category": "Cooking",
  "price": "€85",
  "duration": "3 hours",
  "featured_media": "https://...",
  "status": "open"
}
```

### Format Job
```json
{
  "id": 1,
  "slug": "head-chef",
  "title": {"rendered": "Head Chef"},
  "excerpt": {"rendered": "Short description"},
  "content": {"rendered": "<p>Full description</p>"},
  "venue_id": "stade-jean-bouin",
  "department": "Culinary",
  "job_type": "Full-time",
  "salary": "$150 - $200 / hour",
  "requirements": ["5+ years experience", "..."],
  "benefits": ["Competitive pay", "..."],
  "featured_media": "https://..."
}
```

## 🐛 Dépannage

### Les permaliens ne fonctionnent pas
1. Allez dans `Réglages > Permaliens`
2. Cliquez sur "Enregistrer les modifications" sans rien changer
3. Vérifiez que le fichier `.htaccess` est accessible en écriture

### Les endpoints retournent 404
1. Assurez-vous que le thème est activé
2. Rafraîchissez les permaliens (voir ci-dessus)
3. Vérifiez que `rest_api_init` est bien appelé

### Les images ne s'affichent pas
1. Vérifiez que les images mises en avant sont définies
2. Utilisez des URLs complètes pour les images
3. Configurez correctement les permissions du dossier `uploads`

## 📄 Licence

GPL v2 ou ultérieur

## 👥 Support

Pour toute question ou problème, contactez l'équipe de développement Eat Is Family.

---

**Version** : 1.0.0  
**Dernière mise à jour** : Janvier 2026
