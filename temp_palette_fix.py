from pathlib import Path
root = Path('resources/views/admin')
repls = [
    ('bg-white', 'bg-cream-50'),
    ('border border-slate-200/80', 'border border-walnut-800/10'),
    ('border border-slate-200', 'border border-walnut-800/10'),
    ('text-slate-700', 'text-walnut-800'),
    ('hover:bg-slate-50', 'hover:bg-cream-100'),
    ('bg-slate-50', 'bg-cream-100'),
    ('text-slate-900', 'text-walnut-900'),
    ('text-slate-500', 'text-muted'),
    ('focus:ring-indigo-600', 'focus:ring-gold-500'),
    ('focus:ring-slate-950', 'focus:ring-gold-500'),
    ('bg-amber-600', 'bg-gold-600'),
    ('hover:bg-amber-700', 'hover:bg-gold-700'),
    ('text-amber-700', 'text-gold-700'),
    ('border-amber-200', 'border-gold-200'),
    ('bg-amber-50', 'bg-gold-50'),
    ('text-amber-400', 'text-gold-400'),
    ('focus:ring-indigo-650', 'focus:ring-gold-500'),
]
changed = []
for path in root.rglob('*.blade.php'):
    text = path.read_text(encoding='utf-8')
    new = text
    for old, newv in repls:
        new = new.replace(old, newv)
    if new != text:
        path.write_text(new, encoding='utf-8')
        changed.append(str(path))
print('\n'.join(changed))
