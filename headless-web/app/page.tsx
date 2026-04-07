import Link from "next/link";

import { Hero } from "@/components/hero";
import { PostGrid } from "@/components/post-grid";
import { RichContent } from "@/components/rich-content";
import { getContentNodeByUri, getHomepageConfig, getLatestPosts, getSiteConfig } from "@/lib/wordpress";

export const revalidate = 300;

export default async function HomePage() {
  const [site, homepage, posts] = await Promise.all([getSiteConfig(), getHomepageConfig(), getLatestPosts(9)]);
  const staticPage = homepage.static_page ? await getContentNodeByUri(homepage.static_page.uri) : null;

  return (
    <div className="space-y-12">
      <Hero
        eyebrow="Headless WordPress"
        title={site.site.name}
        description={site.site.description || "以 Next.js 重建前台层，保留 WordPress 的内容管理能力。"}
        accent="Modern SSR Front-end"
      />

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
