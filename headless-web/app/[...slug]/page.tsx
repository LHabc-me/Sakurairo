import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";

import { RichContent } from "@/components/rich-content";
import { Toc } from "@/components/toc";
import { extractToc, formatDate } from "@/lib/content";
import { getContentNodeByUri, getPostExtras } from "@/lib/wordpress";

type ContentPageProps = {
  params: Promise<{ slug: string[] }>;
};

export async function generateMetadata({ params }: ContentPageProps): Promise<Metadata> {
  const { slug } = await params;
  const node = await getContentNodeByUri(`/${slug.join("/")}/`);
  if (!node) {
    return {};
  }

  return {
    title: node.title,
    description: ("excerpt" in node && node.excerpt ? node.excerpt.replace(/<[^>]+>/g, " ").trim() : "")
  };
}

export default async function ContentPage({ params }: ContentPageProps) {
  const { slug } = await params;
  const uri = `/${slug.join("/")}/`;
  const node = await getContentNodeByUri(uri);
  if (!node) {
    notFound();
  }

  const extras = await getPostExtras(node.databaseId);
  const contentHtml = extras?.rendered.content || ("content" in node ? node.content || "" : "");
  const cover = extras?.cover || node.featuredImage?.node?.sourceUrl || "";
  const toc = extractToc(contentHtml);
  const categories = node.__typename === "Post" ? node.categories?.nodes || [] : [];
  const tags = node.__typename === "Post" ? node.tags?.nodes || [] : [];

  return (
    <div className="space-y-10">
      <section className="grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-end">
        <div className="space-y-4">
          <div className="text-[0.72rem] uppercase tracking-[0.35em] text-stone-500">{node.__typename === "Post" ? "Article" : "Page"}</div>
          <h1 className="text-4xl font-semibold tracking-[-0.05em] text-stone-950 sm:text-5xl">{node.title}</h1>
          {"date" in node && node.date ? (
            <div className="flex flex-wrap gap-4 text-sm text-stone-600">
              <span>发布于 {formatDate(node.date)}</span>
              {extras?.reading.word_count ? <span>{extras.reading.word_count} 字</span> : null}
              {node.__typename === "Post" && node.author?.node?.name ? <span>作者 {node.author.node.name}</span> : null}
            </div>
          ) : null}
          {node.__typename === "Post" && (categories.length > 0 || tags.length > 0) ? (
            <div className="flex flex-wrap gap-2 text-xs text-stone-600">
              {categories.map((category) => (
                <Link
                  key={`category-${category.slug}`}
                  href={`/category/${category.slug}`}
                  className="rounded-full border border-stone-300 px-3 py-1 transition hover:border-[color:var(--accent-strong)] hover:text-[color:var(--accent-strong)]"
                >
                  分类 · {category.name}
                </Link>
              ))}
              {tags.map((tag) => (
                <Link
                  key={`tag-${tag.slug}`}
                  href={`/tag/${tag.slug}`}
                  className="rounded-full border border-stone-300 px-3 py-1 transition hover:border-[color:var(--accent-strong)] hover:text-[color:var(--accent-strong)]"
                >
                  标签 · {tag.name}
                </Link>
              ))}
            </div>
          ) : null}
          {extras?.ai_excerpt ? (
            <div className="rounded-[1.5rem] border border-[color:var(--accent)]/20 bg-white/70 p-5 text-sm leading-7 text-stone-700">
              <div className="mb-2 text-xs uppercase tracking-[0.25em] text-[color:var(--accent-strong)]">AI Excerpt</div>
              {extras.ai_excerpt}
            </div>
          ) : null}
        </div>

        <div className="overflow-hidden rounded-[2rem] border border-white/60 bg-white/70 shadow-[0_18px_60px_rgba(110,78,56,0.08)]">
          {cover ? (
            // eslint-disable-next-line @next/next/no-img-element
            <img src={cover} alt={node.title} className="h-full min-h-64 w-full object-cover" />
          ) : (
            <div className="flex min-h-64 items-end bg-[linear-gradient(140deg,_rgba(61,41,33,0.95),_rgba(211,122,93,0.72))] p-6 text-sm uppercase tracking-[0.25em] text-white/70">
              Shinonomeiro
            </div>
          )}
        </div>
      </section>

      <section className="grid gap-8 lg:grid-cols-[minmax(0,1fr)_280px]">
        <div className="rounded-[2rem] border border-white/60 bg-white/75 p-6 shadow-[0_18px_60px_rgba(110,78,56,0.08)] sm:p-10">
          <RichContent html={contentHtml} />
        </div>
        <div className="space-y-5">
          <Toc items={toc} />
          {node.__typename === "Post" && (extras?.navigation?.previous?.uri || extras?.navigation?.next?.uri) ? (
            <aside className="rounded-[1.5rem] border border-stone-200/80 bg-white/75 p-5">
              <div className="mb-3 text-xs uppercase tracking-[0.28em] text-stone-500">继续阅读</div>
              <div className="space-y-3 text-sm">
                {extras?.navigation?.previous?.uri ? (
                  <Link href={extras.navigation.previous.uri} className="block transition hover:text-[color:var(--accent-strong)]">
                    ← {extras.navigation.previous.title || "上一篇"}
                  </Link>
                ) : null}
                {extras?.navigation?.next?.uri ? (
                  <Link href={extras.navigation.next.uri} className="block transition hover:text-[color:var(--accent-strong)]">
                    → {extras.navigation.next.title || "下一篇"}
                  </Link>
                ) : null}
              </div>
            </aside>
          ) : null}
        </div>
      </section>
    </div>
  );
}
