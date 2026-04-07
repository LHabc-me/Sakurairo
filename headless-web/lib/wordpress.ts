import { GraphQLClient, gql } from "graphql-request";

import { getWordpressGraphqlEndpoint, getWordpressRestBaseUrl } from "@/lib/env";
import type { ContentNode, HomepageConfig, PostExtras, SimplePostCard, SiteConfig } from "@/lib/types";

function createGraphQLClient(): GraphQLClient {
  return new GraphQLClient(getWordpressGraphqlEndpoint(), {
    fetch
  });
}

async function fetchBridge<T>(path: string, init?: RequestInit): Promise<T> {
  const response = await fetch(`${getWordpressRestBaseUrl()}/shinonomeiro-headless/v1${path}`, {
    ...init,
    headers: {
      "Content-Type": "application/json; charset=utf-8",
      ...(init?.headers || {})
    },
    next: {
      revalidate: 300
    }
  });

  if (!response.ok) {
    throw new Error(`Bridge 请求失败: ${response.status} ${path}`);
  }

  return (await response.json()) as T;
}

export async function getSiteConfig(): Promise<SiteConfig> {
  return fetchBridge<SiteConfig>("/site");
}

export async function getHomepageConfig(): Promise<HomepageConfig> {
  return fetchBridge<HomepageConfig>("/homepage");
}

export async function getPostExtras(databaseId: number): Promise<PostExtras | null> {
  try {
    return await fetchBridge<PostExtras>(`/posts/${databaseId}/extras`);
  } catch {
    return null;
  }
}

const postCardFragment = gql`
  fragment PostCardFields on Post {
    id
    databaseId
    title
    slug
    uri
    excerpt
    date
    modified
    featuredImage {
      node {
        sourceUrl
        altText
      }
    }
    categories {
      nodes {
        name
        slug
      }
    }
    tags {
      nodes {
        name
        slug
      }
    }
  }
`;

export async function getLatestPosts(first = 12): Promise<SimplePostCard[]> {
  const query = gql`
    ${postCardFragment}
    query LatestPosts($first: Int!) {
      posts(first: $first, where: { status: PUBLISH }) {
        nodes {
          ...PostCardFields
        }
      }
    }
  `;

  const data = await createGraphQLClient().request<{ posts: { nodes: SimplePostCard[] } }>(query, { first });
  return data.posts.nodes;
}

export async function getContentNodeByUri(uri: string): Promise<ContentNode | null> {
  const normalizedUri = uri.startsWith("/") ? uri : `/${uri}`;
  const query = gql`
    query ContentNodeByUri($uri: ID!) {
      contentNode(id: $uri, idType: URI) {
        __typename
        ... on Post {
          databaseId
          title
          slug
          uri
          date
          modified
          excerpt
          content
          featuredImage {
            node {
              sourceUrl
              altText
            }
          }
          categories {
            nodes {
              name
              slug
            }
          }
          tags {
            nodes {
              name
              slug
            }
          }
          author {
            node {
              name
              slug
            }
          }
        }
        ... on Page {
          databaseId
          title
          slug
          uri
          date
          modified
          excerpt
          content
          featuredImage {
            node {
              sourceUrl
              altText
            }
          }
        }
      }
    }
  `;

  const data = await createGraphQLClient().request<{ contentNode: ContentNode | null }>(query, {
    uri: normalizedUri
  });
  return data.contentNode;
}

export async function getCategoryPosts(slug: string, first = 12): Promise<{ name: string; description: string; posts: SimplePostCard[] } | null> {
  const query = gql`
    ${postCardFragment}
    query CategoryPosts($slug: ID!, $first: Int!) {
      category(id: $slug, idType: SLUG) {
        name
        description
        posts(first: $first, where: { status: PUBLISH }) {
          nodes {
            ...PostCardFields
          }
        }
      }
    }
  `;

  const data = await createGraphQLClient().request<{
    category: {
      name: string;
      description: string;
      posts: { nodes: SimplePostCard[] };
    } | null;
  }>(query, { slug, first });

  if (!data.category) {
    return null;
  }

  return {
    name: data.category.name,
    description: data.category.description,
    posts: data.category.posts.nodes
  };
}

export async function getTagPosts(slug: string, first = 12): Promise<{ name: string; description: string; posts: SimplePostCard[] } | null> {
  const query = gql`
    ${postCardFragment}
    query TagPosts($slug: ID!, $first: Int!) {
      tag(id: $slug, idType: SLUG) {
        name
        description
        posts(first: $first, where: { status: PUBLISH }) {
          nodes {
            ...PostCardFields
          }
        }
      }
    }
  `;

  const data = await createGraphQLClient().request<{
    tag: {
      name: string;
      description: string;
      posts: { nodes: SimplePostCard[] };
    } | null;
  }>(query, { slug, first });

  if (!data.tag) {
    return null;
  }

  return {
    name: data.tag.name,
    description: data.tag.description,
    posts: data.tag.posts.nodes
  };
}

export async function searchPosts(term: string, first = 12): Promise<SimplePostCard[]> {
  if (!term.trim()) {
    return [];
  }

  const query = gql`
    ${postCardFragment}
    query SearchPosts($term: String!, $first: Int!) {
      posts(first: $first, where: { search: $term, status: PUBLISH }) {
        nodes {
          ...PostCardFields
        }
      }
    }
  `;

  const data = await createGraphQLClient().request<{ posts: { nodes: SimplePostCard[] } }>(query, {
    term,
    first
  });

  return data.posts.nodes;
}

export async function getAuthorPosts(slug: string, first = 12): Promise<{ name: string; description: string; posts: SimplePostCard[] } | null> {
  const query = gql`
    ${postCardFragment}
    query AuthorPosts($slug: ID!, $first: Int!) {
      user(id: $slug, idType: SLUG) {
        name
        description
        posts(first: $first, where: { status: PUBLISH }) {
          nodes {
            ...PostCardFields
          }
        }
      }
    }
  `;

  const data = await createGraphQLClient().request<{
    user: {
      name: string;
      description: string;
      posts: { nodes: SimplePostCard[] };
    } | null;
  }>(query, { slug, first });

  if (!data.user) {
    return null;
  }

  return {
    name: data.user.name,
    description: data.user.description,
    posts: data.user.posts.nodes
  };
}

export async function getArchivePosts(first = 24): Promise<SimplePostCard[]> {
  return getLatestPosts(first);
}
