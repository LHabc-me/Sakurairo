import type { TocItem } from "@/lib/content";

type TocProps = {
  items: TocItem[];
};

export function Toc({ items }: TocProps) {
  if (items.length === 0) {
    return null;
  }

  return (
    <aside className="rounded-[1.5rem] border border-stone-200/80 bg-white/75 p-5">
      <div className="mb-3 text-xs uppercase tracking-[0.28em] text-stone-500">目录</div>
      <ol className="space-y-2 text-sm leading-6 text-stone-700">
        {items.map((item) => (
          <li key={item.id} style={{ paddingLeft: `${(item.level - 1) * 12}px` }}>
            <a href={`#${item.id}`} className="transition hover:text-[color:var(--accent-strong)]">
              {item.text}
            </a>
          </li>
        ))}
      </ol>
    </aside>
  );
}
