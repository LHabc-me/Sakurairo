import Link from "next/link";

import { formatDate, stripHtml } from "@/lib/content";
import type { SimplePostCard } from "@/lib/types";

type PostCardProps = {
  post: SimplePostCard;
};

export function PostCard({ post }: PostCardProps) {
  const imageUrl = post.featuredImage?.node?.sourceUrl || "";
  const category = post.categories?.nodes?.[0];
  const excerpt = stripHtml(post.excerpt || "").replace(/\s+/g, " ").trim();

  return (
    <article className="group flex h-full flex-col justify-between overflow-hidden rounded-[1.75rem] border border-white/60 bg-white/70 shadow-[0_14px_40px_rgba(92,67,50,0.08)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_60px_rgba(92,67,50,0.14)]">
      <Link href={post.uri} className="block">
        <div className="aspect-[1.45/1] overflow-hidden bg-stone-200">
          {imageUrl ? (
            // eslint-disable-next-line @next/next/no-img-element
            <img
              src={imageUrl}
              alt={post.featuredImage?.node?.altText || post.title}
              className="h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]"
            />
          ) : (
            <div className="flex h-full items-end bg-[linear-gradient(140deg,_rgba(61,41,33,0.92),_rgba(211,122,93,0.72))] p-6 text-sm uppercase tracking-[0.28em] text-white/75">
              No Cover
            </div>
          )}
        </div>
      </Link>

      <div className="flex flex-1 flex-col px-6 py-5">
        <div className="mb-3 flex items-center gap-3 text-xs uppercase tracking-[0.25em] text-stone-500">
          <span>{formatDate(post.date)}</span>
          {category ? <span>{category.name}</span> : null}
        </div>
        <Link href={post.uri} className="text-xl font-semibold leading-8 tracking-[-0.03em] text-stone-950">
          {post.title}
        </Link>
        <p className="mt-3 line-clamp-3 text-sm leading-7 text-stone-600">{excerpt || "这篇文章暂无摘要，点击进入查看完整内容。"}</p>
      </div>
    </article>
  );
}
