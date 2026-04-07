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
