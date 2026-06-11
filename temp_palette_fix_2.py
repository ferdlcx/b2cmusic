import re
from pathlib import Path

root = Path('resources/views/admin')

replacements = {
    # Indigo to gold
    'bg-indigo-50/20': 'bg-gold-50/20',
    'bg-indigo-50': 'bg-gold-50',
    'bg-indigo-100': 'bg-gold-100',
    'bg-indigo-600': 'bg-gold-600',
    'border-indigo-200': 'border-gold-200',
    'hover:bg-indigo-700': 'hover:bg-gold-700',
    'text-indigo-600': 'text-gold-600',
    'text-indigo-650': 'text-gold-600',
    'text-indigo-700': 'text-gold-700',
    
    # Slate to walnut (adjusting values)
    'bg-slate-100': 'bg-cream-100',
    'bg-slate-950': 'bg-walnut-950',
    'border-slate-100': 'border-walnut-800/10',
    'border-slate-150': 'border-walnut-800/15',
    'border-slate-50': 'border-walnut-800/5',
    'border-slate-900': 'border-walnut-900',
    'focus:border-slate-400': 'focus:border-walnut-400',
    'hover:bg-slate-800': 'hover:bg-walnut-800',
    'hover:bg-slate-850': 'hover:bg-walnut-800',
    'hover:bg-slate-900': 'hover:bg-walnut-900',
    'hover:text-slate-950': 'hover:text-walnut-950',
    'text-slate-200': 'text-walnut-200',
    'text-slate-300': 'text-walnut-300',
    'text-slate-350': 'text-walnut-300',
    'text-slate-400': 'text-walnut-400',
    'text-slate-600': 'text-walnut-600',
    'text-slate-650': 'text-walnut-600',
    'text-slate-800': 'text-walnut-800',
    'text-slate-950': 'text-walnut-950',
}

changed_files = []

for path in root.rglob('*.blade.php'):
    text = path.read_text(encoding='utf-8')
    new_text = text
    for old, new in replacements.items():
        # Only replace exact word boundaries for classes, but actually string replace is mostly fine if we order them correctly.
        # Let's sort by length descending to avoid partial replacements
        pass
        
    for old in sorted(replacements.keys(), key=len, reverse=True):
        new = replacements[old]
        new_text = new_text.replace(old, new)
        
    if new_text != text:
        path.write_text(new_text, encoding='utf-8')
        changed_files.append(str(path))

print(f"Changed {len(changed_files)} files:")
for f in changed_files:
    print(f)
