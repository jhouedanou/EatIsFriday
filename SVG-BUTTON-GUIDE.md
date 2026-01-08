# 🎨 Guide d'utilisation du bouton SVG organique

Basé sur le fichier `Button.svg` avec bordures organiques dessinées à la main.

## 📦 Deux solutions disponibles

### 1️⃣ Composant Vue (Recommandé)
**Fichier:** `app/components/SvgButton.vue`

#### Avantages
- ✅ Couleurs facilement personnalisables via props
- ✅ Gestion complète des événements
- ✅ TypeScript avec typage des props
- ✅ Meilleure performance (SVG inline optimisé)
- ✅ Slot pour contenu personnalisé

#### Utilisation

```vue
<template>
  <!-- Bouton primaire (défaut) -->
  <SvgButton @click="handleClick">
    Get in touch
  </SvgButton>

  <!-- Bouton secondaire -->
  <SvgButton variant="secondary" size="large">
    Learn more
  </SvgButton>

  <!-- Bouton désactivé -->
  <SvgButton :disabled="true">
    Disabled
  </SvgButton>

  <!-- Couleur personnalisée -->
  <SvgButton fill-color="#4A90E2">
    Custom color
  </SvgButton>
</template>

<script setup>
const handleClick = (event) => {
  console.log('Button clicked!', event)
}
</script>
```

#### Props disponibles

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `variant` | `'primary' \| 'secondary' \| 'success' \| 'danger'` | `'primary'` | Style prédéfini du bouton |
| `size` | `'small' \| 'medium' \| 'large'` | `'medium'` | Taille du bouton |
| `disabled` | `boolean` | `false` | État désactivé |
| `fillColor` | `string` | - | Couleur de remplissage personnalisée (hex, rgb, etc.) |

#### Variantes de couleur

```vue
<!-- Rouge/rose (#F9375B) - texte blanc -->
<SvgButton variant="primary">Primary</SvgButton>

<!-- Bleu (#4A90E2) - texte noir -->
<SvgButton variant="secondary">Secondary</SvgButton>

<!-- Vert (#27AE60) - texte noir -->
<SvgButton variant="success">Success</SvgButton>

<!-- Rouge (#E74C3C) - texte blanc -->
<SvgButton variant="danger">Danger</SvgButton>
```

#### Tailles

```vue
<!-- 120px min-width, 42px hauteur -->
<SvgButton size="small">Small</SvgButton>

<!-- 160px min-width, 52px hauteur -->
<SvgButton size="medium">Medium</SvgButton>

<!-- 200px min-width, 62px hauteur -->
<SvgButton size="large">Large</SvgButton>
```

---

### 2️⃣ Classes CSS
**Fichier:** `app/assets/scss/_svg-button.scss`

#### Avantages
- ✅ Pas besoin d'importer un composant
- ✅ Utilisable dans du HTML pur
- ✅ Poids léger

#### Inconvénients
- ⚠️ Couleurs SVG fixes (difficiles à personnaliser)
- ⚠️ Nécessite un encodage data URL pour changer les couleurs

#### Utilisation

```html
<!-- Bouton medium primaire -->
<button class="svg-btn-md svg-btn-primary">
  Get in touch
</button>

<!-- Bouton large secondaire -->
<button class="svg-btn-lg svg-btn-secondary">
  Learn more
</button>

<!-- Bouton small -->
<button class="svg-btn-sm svg-btn-primary" disabled>
  Disabled
</button>
```

#### Import du SCSS

Dans `app/assets/scss/main.scss` :

```scss
@import './svg-button';
```

#### Classes disponibles

**Tailles :**
- `.svg-btn-sm` - Petit (120px × 42px)
- `.svg-btn-md` - Moyen (160px × 52px)
- `.svg-btn-lg` - Grand (200px × 200px)

**Variantes :**
- `.svg-btn-primary` - Texte blanc
- `.svg-btn-secondary` - Texte noir
- `.svg-btn-success` - Texte noir
- `.svg-btn-danger` - Texte blanc

---

## 🎯 Exemples dans le projet

### Remplacement dans Header.vue

**Avant :**
```vue
<OrganicButton variant="primary">Get in touch</OrganicButton>
```

**Après (composant) :**
```vue
<SvgButton variant="primary">Get in touch</SvgButton>
```

**Après (classe CSS) :**
```vue
<button class="svg-btn-md svg-btn-primary">
  Get in touch
</button>
```

### Utilisation avec Nuxt Link

```vue
<NuxtLink to="/contact" custom v-slot="{ navigate }">
  <SvgButton @click="navigate">
    Contact us
  </SvgButton>
</NuxtLink>
```

---

## 🎨 Personnalisation avancée

### Modifier les couleurs par défaut (Composant)

Dans `SvgButton.vue`, modifiez l'objet `colors` :

```typescript
const colors = {
  primary: '#F9375B',    // Rose/rouge
  secondary: '#4A90E2',  // Bleu
  success: '#27AE60',    // Vert
  danger: '#E74C3C'      // Rouge
}
```

### Modifier les couleurs (Classes CSS)

Dans `_svg-button.scss`, modifiez les variables :

```scss
$svg-button-primary: #F9375B;
$svg-button-secondary: #4A90E2;
$svg-button-success: #27AE60;
$svg-button-danger: #E74C3C;
```

**Note importante :** Pour changer la couleur du SVG avec les classes CSS, vous devrez :
1. Régénérer le data URL SVG avec la nouvelle couleur
2. Encoder les couleurs hex pour l'URL (`#` → `%23`)

### Modifier les tailles

**Composant :**
```scss
&.small {
  padding: 0.5rem 1.25rem;
  font-size: 0.875rem;
  min-width: 120px;
  height: 42px;
}
```

**Classes CSS :**
```scss
.svg-btn-sm {
  padding: 0.5rem 1.25rem;
  font-size: 0.875rem;
  min-width: 120px;
  height: 42px;
}
```

### Ajouter des animations personnalisées

```scss
.svg-button:hover {
  animation: wobble 0.5s ease;
}

@keyframes wobble {
  0%, 100% { transform: translateY(-2px) rotate(0deg); }
  25% { transform: translateY(-2px) rotate(1deg); }
  75% { transform: translateY(-2px) rotate(-1deg); }
}
```

---

## 📁 Structure des fichiers

```
app/
├── components/
│   └── SvgButton.vue          # Composant Vue réutilisable
├── assets/
│   └── scss/
│       ├── _svg-button.scss   # Classes CSS réutilisables
│       └── main.scss          # Import des styles
public/
└── images/
    └── Button.svg             # Fichier SVG original
```

---

## 🚀 Migration depuis OrganicButton

Si vous utilisez actuellement `OrganicButton.vue`, voici comment migrer :

1. **Remplacer les imports :**
   ```diff
   - import OrganicButton from '~/components/OrganicButton.vue'
   + import SvgButton from '~/components/SvgButton.vue'
   ```

2. **Remplacer les usages :**
   ```diff
   - <OrganicButton variant="primary">Text</OrganicButton>
   + <SvgButton variant="primary">Text</SvgButton>
   ```

3. **Les props sont identiques**, la migration est transparente !

---

## ✨ Caractéristiques techniques

### SVG Paths extraits de Button.svg

- **Fill path :** Rectangle arrondi (base du bouton)
- **Stroke path :** ~4000+ coordonnées pour l'effet dessiné à la main
- **Couleurs originales :**
  - Fill: `#F9375B` (rose/rouge)
  - Stroke: `#0D0A00` (noir)
- **ViewBox :** `0 0 185 65`
- **Ratio d'aspect :** ~2.85:1

### Effets appliqués

- **Drop shadow :** Ombre portée suivant la forme organique
- **Hover :** Translation Y + agrandissement léger du SVG
- **Active :** Réduction de l'ombre pour effet d'enfoncement
- **Transition :** 0.2s ease pour fluidité

### Performance

- ✅ SVG inline optimisé (pas de requête HTTP)
- ✅ GPU accelerated transforms
- ✅ Pas de dépendance externe
- ✅ ~15KB pour le composant complet

---

## 🐛 Résolution de problèmes

### Le bouton ne s'affiche pas correctement

1. Vérifiez que le parent a un `display: flex` ou `display: block`
2. Assurez-vous que le `z-index` du parent ne masque pas le bouton
3. Vérifiez que les polices sont chargées (Recoleta)

### Les couleurs ne changent pas (classes CSS)

Les classes CSS utilisent un SVG encodé en data URL. Pour changer les couleurs, vous devez :
1. Utiliser le composant Vue (recommandé)
2. Ou régénérer le data URL avec vos couleurs personnalisées

### Le texte déborde

Ajustez le `min-width` ou utilisez une taille de bouton plus grande :
```vue
<SvgButton size="large">Long button text</SvgButton>
```

---

## 📖 Ressources

- [Composant source](../app/components/SvgButton.vue)
- [Styles SCSS](../app/assets/scss/_svg-button.scss)
- [SVG original](../public/images/Button.svg)
- [Documentation Nuxt](https://nuxt.com/docs/guide/directory-structure/components)

---

**Créé à partir du fichier Button.svg**  
Design : Bordures organiques dessinées à la main  
Technologie : Vue 3 + TypeScript + SCSS
