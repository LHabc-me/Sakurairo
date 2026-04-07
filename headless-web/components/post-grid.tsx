import type { SimplePostCard } from "@/lib/types";
import { PostCard } from "@/components/post-card";

type PostGridProps = {
  title: string;
  posts: SimplePostCard[];
  description?: string;
};

export function PostGrid({ title, posts, description }: PostGridProps) {
  return (
    <section className="mt-14">
      <div className="mb-6 flex flex-col gap-2">
        <div className="text-[0.72rem] uppercase tracking-[0.35em] text-stone-500">Collection</div>
        <h2 className="text-3xl font-semibold tracking-[-0.04em] text-stone-950">{title}</h2>
        {description ? <p className="max-w-3xl text-sm leading-7 text-stone-600">{description}</p> : null}
      </div>

      <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        {posts.map((post) => (
          <PostCard key={post.id} post={post} />
        ))}
      </div>
    </section>
  );
}
