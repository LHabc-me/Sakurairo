import { Hero } from "@/components/hero";
import { PostGrid } from "@/components/post-grid";
import { getArchivePosts } from "@/lib/wordpress";

export default async function ArchivePage() {
  const posts = await getArchivePosts(24);

  return (
    <div className="space-y-10">
      <Hero eyebrow="Archive" title="文章归档" description="首版归档页以发布时间倒序展示最近的文章。" accent="Chronological Feed" />
      <PostGrid title="最近更新" posts={posts} />
    </div>
  );
}
