import { notFound } from "next/navigation";

import { Hero } from "@/components/hero";
import { PostGrid } from "@/components/post-grid";
import { getTagPosts } from "@/lib/wordpress";

type TagPageProps = {
  params: Promise<{ slug: string }>;
};

export default async function TagPage({ params }: TagPageProps) {
  const { slug } = await params;
  const tag = await getTagPosts(slug);
  if (!tag) {
    notFound();
  }

  return (
    <div className="space-y-10">
      <Hero eyebrow="Tag" title={tag.name} description={tag.description || "标签归档页。"} accent="Tag Collection" />
      <PostGrid title={`${tag.name} · 文章`} posts={tag.posts} />
    </div>
  );
}
