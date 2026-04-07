import Link from "next/link";
import type { CSSProperties, ReactNode } from "react";

import type { MenuItem, SiteConfig } from "@/lib/types";

type SiteShellProps = {
  site: SiteConfig;
  children: ReactNode;
};

function normalizeMenuUrl(url: string): string {
  try {
    const parsed = new URL(url);
    return `${parsed.pathname}${parsed.search}${parsed.hash}` || "/";
  } catch {
    return url || "/";
  }
}

type MenuNode = MenuItem & {
  children: MenuNode[];
};

function buildMenuTree(items: MenuItem[]): MenuNode[] {
  const nodes = new Map<number, MenuNode>();
  const roots: MenuNode[] = [];

  for (const item of items) {
    nodes.set(item.id, { ...item, children: [] });
  }

  for (const item of items) {
    const current = nodes.get(item.id);
    if (!current) {
      continue;
    }

    if (item.parent && nodes.has(item.parent)) {
      nodes.get(item.parent)?.children.push(current);
    } else {
      roots.push(current);
    }
  }

  return roots.sort((left, right) => left.order - right.order);
}

function MenuLink({ item }: { item: MenuNode }) {
  const href = normalizeMenuUrl(item.url);

  if (item.children.length === 0) {
    return (
      <Link href={href} className="transition hover:text-[color:var(--accent-strong)]">
        {item.title}
      </Link>
    );
  }

  return (
    <div className="group relative">
      <Link href={href === "#" ? "/" : href} className="flex items-center gap-2 transition hover:text-[color:var(--accent-strong)]">
        <span>{item.title}</span>
        <span className="text-[10px]">▼</span>
      </Link>
      <div className="invisible absolute left-0 top-full z-20 mt-3 min-w-44 translate-y-2 rounded-2xl border border-stone-200/80 bg-white/95 p-2 opacity-0 shadow-[0_18px_40px_rgba(45,31,24,0.12)] transition duration-200 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
        {item.children
          .sort((left, right) => left.order - right.order)
          .map((child) => (
            <Link
              key={child.id}
              href={normalizeMenuUrl(child.url)}
              className="block rounded-xl px-3 py-2 text-sm text-stone-700 transition hover:bg-stone-100 hover:text-[color:var(--accent-strong)]"
            >
              {child.title}
            </Link>
          ))}
      </div>
    </div>
  );
}

export function SiteShell({ site, children }: SiteShellProps) {
  const brandText = site.branding.text_logo.text || site.site.name;
  const menuTree = buildMenuTree(site.menus.primary);

  return (
    <div
      className="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(211,122,93,0.22),_transparent_38%),linear-gradient(180deg,_#f8f1ea_0%,_#f3ece4_36%,_#efe8df_100%)] text-stone-900"
      style={
        {
          ["--accent" as string]: site.theme.skin,
          ["--accent-strong" as string]: site.theme.skin_matching,
          ["--accent-dark" as string]: site.theme.skin_dark
        } as CSSProperties
      }
    >
      <div className="mx-auto flex min-h-screen max-w-7xl flex-col px-5 pb-10 pt-5 sm:px-8 lg:px-10">
        <header className="sticky top-0 z-40 mb-8 border-b border-white/50 bg-[#f8f1ea]/85 backdrop-blur-xl">
          <div className="flex items-center justify-between gap-6 py-4">
            <Link href="/" className="min-w-0">
              <div className="text-[0.7rem] uppercase tracking-[0.35em] text-stone-500">Shinonomeiro</div>
              <div className="truncate text-2xl font-semibold text-stone-950">{brandText}</div>
            </Link>
            <nav className="hidden items-center gap-5 text-sm text-stone-700 md:flex">
              {menuTree.map((item) => (
                <MenuLink key={item.id} item={item} />
              ))}
              <Link href="/search" className="rounded-full border border-stone-300 px-4 py-2 text-xs uppercase tracking-[0.2em]">
                Search
              </Link>
            </nav>
            <details className="md:hidden">
              <summary className="list-none rounded-full border border-stone-300 px-4 py-2 text-xs uppercase tracking-[0.2em] text-stone-700">
                Menu
              </summary>
              <div className="absolute right-5 top-full mt-3 w-72 rounded-[1.5rem] border border-stone-200/80 bg-white/95 p-4 shadow-[0_18px_40px_rgba(45,31,24,0.12)] sm:right-8">
                <div className="space-y-2">
                  {menuTree.map((item) => (
                    <div key={item.id} className="rounded-2xl border border-stone-100 bg-stone-50/70 p-3">
                      <Link href={normalizeMenuUrl(item.url === "#" ? "/" : item.url)} className="block text-sm font-medium text-stone-900">
                        {item.title}
                      </Link>
                      {item.children.length > 0 ? (
                        <div className="mt-2 space-y-1">
                          {item.children
                            .sort((left, right) => left.order - right.order)
                            .map((child) => (
                              <Link key={child.id} href={normalizeMenuUrl(child.url)} className="block text-sm text-stone-600">
                                {child.title}
                              </Link>
                            ))}
                        </div>
                      ) : null}
                    </div>
                  ))}
                  <Link href="/search" className="block rounded-2xl border border-stone-200 px-4 py-3 text-sm text-stone-700">
                    搜索文章
                  </Link>
                </div>
              </div>
            </details>
          </div>
        </header>

        <main className="flex-1">{children}</main>

        <footer className="mt-20 border-t border-stone-300/70 pt-8 text-sm text-stone-600">
          <div className="grid gap-6 md:grid-cols-[1.2fr_0.8fr] md:items-start">
            <div className="space-y-3">
              <div className="text-lg font-medium text-stone-900">{site.site.name}</div>
              <p className="max-w-2xl leading-7">{site.footer.info || site.site.description || "Headless front-end powered by Next.js and WordPress."}</p>
            </div>
            <div className="space-y-3 md:text-right">
              <div className="text-xs uppercase tracking-[0.25em] text-stone-500">Social</div>
              <div className="flex flex-wrap gap-3 md:justify-end">
                {site.social_links.map((link) => (
                  <a
                    key={link.id}
                    href={link.url}
                    target="_blank"
                    rel="noreferrer"
                    className="rounded-full border border-stone-300 px-3 py-1 transition hover:border-[color:var(--accent)] hover:text-[color:var(--accent-strong)]"
                  >
                    {link.label}
                  </a>
                ))}
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>
  );
}
