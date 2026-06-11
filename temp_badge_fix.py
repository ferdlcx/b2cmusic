from pathlib import Path

root = Path('resources/views/admin')

# Map all non-palette colors to walnut/gold equivalents for status badges
replacements = {
    # Blue (shipped) → oak tones
    'bg-blue-50': 'bg-oak-600/10',
    'text-blue-700': 'text-oak-700',
    'text-blue-600': 'text-oak-600',
    'border-blue-200': 'border-oak-600/20',
    
    # Emerald/Green (active/completed/approved) → walnut-950 on gold
    'bg-emerald-50': 'bg-walnut-950/5',
    'bg-emerald-50/30': 'bg-walnut-950/5',
    'text-emerald-700': 'text-walnut-950',
    'text-emerald-800': 'text-walnut-950',
    'text-emerald-600': 'text-walnut-800',
    'border-emerald-200': 'border-walnut-800/20',
    'border-emerald-250': 'border-walnut-800/20',
    'border-emerald-100/50': 'border-walnut-800/10',
    'bg-green-50': 'bg-walnut-950/5',
    'text-green-600': 'text-walnut-950',
    'border-green-600/30': 'border-walnut-800/20',
    
    # Red (rejected/suspended) → keep semantic but soften 
    'bg-red-100': 'bg-rose-50',
    'text-red-800': 'text-rose-700',
    'border-red-200': 'border-rose-200',
    'bg-red-50': 'bg-rose-50',
    'text-red-600': 'text-rose-600',
    'border-red-600': 'border-rose-500',
    'hover:bg-red-600': 'hover:bg-rose-600',
    'border-red-500/30': 'border-rose-500/30',
    'border-red-600/30': 'border-rose-500/30',
    
    # Rose (already close but standardize)
    'border-rose-250': 'border-rose-200',
}

changed_files = []

for path in root.rglob('*.blade.php'):
    text = path.read_text(encoding='utf-8')
    new_text = text
    for old in sorted(replacements.keys(), key=len, reverse=True):
        new = replacements[old]
        new_text = new_text.replace(old, new)
    if new_text != text:
        path.write_text(new_text, encoding='utf-8')
        changed_files.append(str(path))

print(f"Changed {len(changed_files)} files:")
for f in changed_files:
    print(f)
