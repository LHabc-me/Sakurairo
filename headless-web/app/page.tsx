import Link from "next/link";

import { Hero } from "@/components/hero";
import { PostGrid } from "@/components/post-grid";
import { RichContent } from "@/components/rich-content";
import { getContentNodeByUri, getHomepageConfig, getLatestPosts, getSiteConfig } from "@/lib/wordpress";

export const revalidate = 300;

export default async function HomePage() {
  const [site, homepage, posts] = await Promise.all([getSiteConfig(), getHomepageConfig(), getLatestPosts(9)]);
  const staticPage = homepage.static_page ? await getContentNodeByUri(homepage.static_page.uri) : null;
  const exhibitionPosts = posts.slice(0, 3);

  return (
    <div className="space-y-12">
      <Hero
        eyebrow="Headless WordPress"
        title={site.site.name}
        description={site.site.description || "以 Next.js 重建前台层，保留 WordPress 的内容管理能力。"}
        accent="Modern SSR Front-end"
      />

      {homepage.components.includes("exhibition") ? (
        <section className="grid gap-5 lg:grid-cols-[0.95fr_1.05fr]">
          <div className="rounded-[2rem] border border-white/60 bg-[linear-gradient(135deg,_rgba(61,41,33,0.96),_rgba(94,70,56,0.88),_rgba(211,122,93,0.78))] p-8 text-white shadow-[0_22px_60px_rgba(45,31,24,0.18)]">
            <div className="text-[0.72rem] uppercase tracking-[0.35em] text-white/60">{homepage.display_area.title}</div>
            <h2 className="mt-4 text-3xl font-semibold tracking-[-0.04em]">Recent Highlights</h2>
            <p className="mt-4 max-w-xl text-sm leading-7 text-white/78">这一部分用于承接旧主题首页展示区。首版以前三篇最新文章作为重点内容，并保留站点对外链接入口。</p>
            <div className="mt-8 flex flex-wrap gap-3">
              {site.social_links.map((link) => (
                <a key={link.id} href={link.url} target="_blank" rel="noreferrer" className="rounded-full border border-white/25 px-4 py-2 text-xs uppercase tracking-[0.25em] text-white/85 transition hover:border-white/50 hover:bg-white/10">
                  {link.label}
                </a>
              ))}
            </div>
          </div>

          <div className="grid gap-4">
            {exhibitionPosts.map((post, index) => (
              <Link
                key={post.id}
                href={post.uri}
                className="group grid gap-4 rounded-[1.75rem] border border-white/60 bg-white/74 p-5 shadow-[0_16px_40px_rgba(45,31,24,0.08)] transition hover:-translate-y-1 hover:shadow-[0_22px_50px_rgba(45,31,24,0.12)] md:grid-cols-[120px_1fr]"
              >
                <div className="flex aspect-square items-end overflow-hidden rounded-[1.2rem] bg-[linear-gradient(145deg,_rgba(61,41,33,0.98),_rgba(211,122,93,0.76))] p-4 text-xs uppercase tracking-[0.3em] text-white/70">
                  #{index + 1}
                </div>
                <div>
                  <div className="text-[0.68rem] uppercase tracking-[0.28em] text-stone-500">Feature Story</div>
                  <div className="mt-2 text-xl font-semibold leading-8 tracking-[-0.03em] text-stone-950 transition group-hover:text-[color:var(--accent-strong)]">
                    {post.title}
                  </div>
                  <p className="mt-3 text-sm leading-7 text-stone-600">{post.excerpt.replace(/<[^>]+>/g, " ").trim()}</p>
                </div>
              </Link>
            ))}
          </div>
        </section>
      ) : null}

      {homepage.components.includes("static_page") && staticPage && "content" in staticPage && staticPage.content ? (
        <section className="grid gap-6 lg:grid-cols-[0.75fr_1.25fr]">
          <div className="space-y-3">
            <div className="text-[0.72rem] uppercase tracking-[0.35em] text-stone-500">Static Page</div>
            <h2 className="text-3xl font-semibold tracking-[-0.04em] text-stone-950">{staticPage.title}</h2>
            {homepage.static_page?.uri ? (
              <Link href={homepage.static_page.uri} className="inline-flex rounded-full border border-stone-300 px-4 py-2 text-xs uppercase tracking-[0.25em] text-stone-700">
                阅读完整页面
              </Link>
            ) : null}
          </div>
          <div className="rounded-[2rem] border border-white/60 bg-white/72 p-6 shadow-[0_18px_60px_rgba(110,78,56,0.08)]">
            <RichContent html={staticPage.content} />
          </div>
        </section>
      ) : null}

      {homepage.components.includes("primary") ? (
        <PostGrid title={homepage.post_area.title} posts={posts} description="当前站点的最新文章集合，直接从 WordPress GraphQL 获取。" />
      ) : null}
    </div>
  );
}
