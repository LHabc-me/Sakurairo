import type { MetadataRoute } from "next";

import { getSiteUrl } from "@/lib/env";
import type { SimplePostCard } from "@/lib/types";
import { getArchivePosts } from "@/lib/wordpress";

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const siteUrl = getSiteUrl();
  let posts: SimplePostCard[] = [];
  try {
    posts = await getArchivePosts(50);
  } catch {
    posts = [];
  }

  const baseEntries: MetadataRoute.Sitemap = [
    {
      url: siteUrl,
      changeFrequency: "daily",
      priority: 1
    },
    {
      url: `${siteUrl}/archive`,
      changeFrequency: "daily",
      priority: 0.8
    },
    {
      url: `${siteUrl}/search`,
      changeFrequency: "weekly",
      priority: 0.4
    }
  ];

  const postEntries = posts.map((post) => ({
    url: new URL(post.uri, siteUrl).toString(),
    lastModified: post.modified ? new Date(post.modified) : undefined,
    changeFrequency: "weekly" as const,
    priority: 0.7
  }));

  return [...baseEntries, ...postEntries];
}
