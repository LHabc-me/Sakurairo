import { notFound } from "next/navigation";

import { Hero } from "@/components/hero";
import { PostGrid } from "@/components/post-grid";
import { getAuthorPosts } from "@/lib/wordpress";

type AuthorPageProps = {
  params: Promise<{ slug: string }>;
};

export default async function AuthorPage({ params }: AuthorPageProps) {
  const { slug } = await params;
  const author = await getAuthorPosts(slug);
  if (!author) {
    notFound();
  }

  return (
    <div className="space-y-10">
      <Hero eyebrow="Author" title={author.name} description={author.description || "作者文章归档页。"} accent="Author Archive" />
      <PostGrid title={`${author.name} · 文章`} posts={author.posts} />
    </div>
  );
}
