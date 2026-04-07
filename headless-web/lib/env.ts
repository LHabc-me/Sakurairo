function readRequired(name: string): string {
  const value = process.env[name];
  if (!value) {
    throw new Error(`缺少环境变量 ${name}`);
  }
  return value;
}

export function getWordpressGraphqlEndpoint(): string {
  return readRequired("WORDPRESS_GRAPHQL_ENDPOINT");
}

export function getWordpressRestBaseUrl(): string {
  return readRequired("WORDPRESS_REST_BASE_URL").replace(/\/+$/, "");
}

export function getSiteUrl(): string {
  return process.env.NEXT_PUBLIC_SITE_URL || "http://localhost:3000";
}
