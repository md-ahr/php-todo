# Views & UI conventions

---

## Layout composition

Most screens follow this pattern:

1. **`views/components/head.php`** — `<html>`, charset, viewport, Tailwind browser CDN (**`@tailwindcss/browser@4`**), opens `<body>`.
2. **`views/components/header.php`** — primary navigation (desktop + mobile dialog pattern).
3. Page-specific `<main>...</main>` content inline in the view file (or structured sections).
4. **`views/components/footer.php`** — closing markup.

Templates use **`include_once`** for shared fragments so duplicate PHP notices do not repeat sections.

---

## Styling

- **Tailwind utility classes** applied directly in markup (no local PostCSS pipeline in repo).
- CDN script loads Tailwind **at runtime** — acceptable for demos; production teams may prefer compiled CSS for CSP and offline resilience.

---

## Escaping & XSS

Dynamic text must use **`htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`**.

Many views define a tiny **`esc()`** closure alias — **stay consistent** within each template.

Avoid **`echo $variable`** raw inside HTML attributes.

---

## Forms & POST conventions

| Pattern | Usage |
|---------|--------|
| **`method="post"`** | Mutations |
| **`_token`** hidden field | CSRF — pair with **`csrf_token()`** |
| **`_action`** | Semantic verb for composite controllers (`TodosController`) |
| **`$_SESSION['_flash_*]` / notices** | POST → redirect → GET flash messaging |

Todos preserve filter state via **`state_*`** hidden inputs derived from **`TodoListState::fromPostPrefixes`**.

---

## Accessibility hints

Navigation uses landmarks (`<header>`, `<nav aria-label>`). When extending forms:

- Associate `<label>` with controls (`for` / `id`).
- Preserve visible focus rings (`focus-visible:*` utilities already appear on several interactive elements).

---

## Static error page

**`404.view.php`** — verify HTTP status policy under **`NotFoundController`** if crawlers or APIs depend on accurate codes.
