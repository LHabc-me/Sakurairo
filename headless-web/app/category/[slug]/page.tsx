import { notFound } from "next/navigation";

import { Hero } from "@/components/hero";
import { PostGrid } from "@/components/post-grid";
import { getCategoryPosts } from "@/lib/wordpress";

type CategoryPageProps = {
  params: Promise<{ slug: string }>;
};

export default async function CategoryPage({ params }: CategoryPageProps) {
  const { slug } = await params;
  const category = await getCategoryPosts(slug);
  if (!category) {
    notFound();
  }

  return (
    <div className="space-y-10">
      <Hero eyebrow="Category" title={category.name} description={category.description || "分类归档页。"} accent="Taxonomy Archive" />
      <PostGrid title={`${category.name} · 文章`} posts={category.posts} />
    </div>
  );
}
