import { EmptyState } from "@/components/empty-state";
import { Hero } from "@/components/hero";
import { PostGrid } from "@/components/post-grid";
import { searchPosts } from "@/lib/wordpress";

type SearchPageProps = {
  searchParams: Promise<Record<string, string | string[] | undefined>>;
};

export default async function SearchPage({ searchParams }: SearchPageProps) {
  const params = await searchParams;
  const term = typeof params.q === "string" ? params.q : typeof params.s === "string" ? params.s : "";
  const posts = await searchPosts(term);

  return (
    <div className="space-y-10">
      <Hero
        eyebrow="Search"
        title={term ? `“${term}” 的搜索结果` : "搜索站点内容"}
        description="输入关键词后，结果直接来自 WordPress GraphQL 内容索引。"
        accent="Content Discovery"
      />

      <section className="rounded-[1.75rem] border border-white/60 bg-white/75 p-5 shadow-[0_14px_40px_rgba(45,31,24,0.08)]">
        <form action="/search" method="get" className="flex flex-col gap-3 sm:flex-row">
          <input
            type="search"
            name="q"
            defaultValue={term}
            placeholder="输入关键词，例如 WordPress"
            className="min-w-0 flex-1 rounded-full border border-stone-300 bg-white px-5 py-3 text-sm text-stone-900 outline-none transition focus:border-[color:var(--accent-strong)]"
          />
          <button
            type="submit"
            className="rounded-full bg-[color:var(--accent-strong)] px-6 py-3 text-sm font-medium text-white transition hover:opacity-90"
          >
            搜索
          </button>
        </form>
      </section>

      {term ? (
        posts.length > 0 ? (
          <PostGrid title="匹配文章" posts={posts} />
        ) : (
          <EmptyState title="没有找到结果" description="当前关键词没有匹配到已发布文章，请换一个关键词继续搜索。" />
        )
      ) : (
        <EmptyState title="请输入关键词" description="访问方式示例：/search?q=WordPress" />
      )}
    </div>
  );
}
