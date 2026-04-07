import Link from "next/link";

import { getContentNodeByUri, getHomepageConfig, getLatestPosts, getPostExtras, getSiteConfig } from "@/lib/wordpress";

export const revalidate = 300;

export default async function HomePage() {
  const [site, homepage, posts] = await Promise.all([getSiteConfig(), getHomepageConfig(), getLatestPosts(10)]);
  const [staticPage, postExtras] = await Promise.all([
    homepage.static_page ? getContentNodeByUri(homepage.static_page.uri) : Promise.resolve(null),
    Promise.all(posts.map((post) => getPostExtras(post.databaseId)))
  ]);

  return (
    <div className="space-y-12">
      {homepage.components.includes("exhibition") ? (
        <section className="exhibition-area-container">
          <h1 className="fes-title">{homepage.display_area.title}</h1>
          <div className="bento-grid">
            {homepage.capsules.length > 0 ? (
              <div className="stat-capsules-container">
                {homepage.capsules.map((capsule) => {
                  if (capsule.type === "link" && capsule.link) {
                    return (
                      <div key={capsule.id} className="stat-capsule link-capsule">
                        <div className="link-avatar">
                          {capsule.link.image ? (
                            // eslint-disable-next-line @next/next/no-img-element
                            <img src={capsule.link.image} alt={capsule.link.name} />
                          ) : null}
                        </div>
                        <div className="link-info">
                          <a href={capsule.link.url} target="_blank" rel="noreferrer">
                            {capsule.link.name}
                          </a>
                          <span className="link-description">{capsule.link.description}</span>
                        </div>
                      </div>
                    );
                  }

                  if (capsule.type === "announcement") {
                    return (
                      <div key={capsule.id} className="stat-capsule announcement-capsule">
                        <i className={capsule.icon} />
                        <div className="capsule-content">
                          <span className="announcement-line first-line">{capsule.label}</span>
                        </div>
                      </div>
                    );
                  }

                  return (
                    <div key={capsule.id} className="stat-capsule">
                      <i className={capsule.icon} />
                      <div className="capsule-content">
                        <span className="capsule-label">{capsule.label}</span>
                        <span className="capsule-value">{capsule.value}</span>
                      </div>
                    </div>
                  );
                })}
              </div>
            ) : null}

            {homepage.exhibition_items.map((item) => (
              <div key={item.id} className="bento-item bento-medium">
                <div className="card-title-wrapper">
                  <h3 className="bento-card-title">{item.title}</h3>
                </div>
                <a href={item.link} target="_blank" rel="noreferrer" className="card-link">
                  <div className="card-image">
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img src={item.image} alt={item.title} loading="lazy" />
                  </div>
                  <div className="card-info">
                    {item.description ? <p className="card-description">{item.description}</p> : null}
                  </div>
                </a>
              </div>
            ))}
          </div>
        </section>
      ) : null}

      {homepage.components.includes("static_page") && staticPage && "content" in staticPage && staticPage.content ? (
        <section className="custom-static-section">
          <h1 className="main-title static-page-title">{staticPage.title}</h1>
          <div className="static-page-content" dangerouslySetInnerHTML={{ __html: staticPage.content }} />
        </section>
      ) : null}

      {homepage.components.includes("primary") ? (
        <div id="primary" className="content-area">
          <main id="main" className="site-main" role="main">
            <h1 className="main-title posts-area-title">{homepage.post_area.title}</h1>
            {posts.map((post, index) => {
              const extras = postExtras[index];
              const cover = post.featuredImage?.node?.sourceUrl || extras?.cover || "";
              const excerpt = extras?.ai_excerpt || post.excerpt.replace(/<[^>]+>/g, " ").trim();

              return (
                <article key={post.id} className="post post-list-thumb" itemScope itemType="http://schema.org/BlogPosting">
                  <div className="post-thumb">
                    <Link href={post.uri}>
                      {cover ? (
                        // eslint-disable-next-line @next/next/no-img-element
                        <img alt="post_img" src={cover} />
                      ) : null}
                    </Link>
                  </div>
                  <div className="post-date">
                    <i className="fa-regular fa-clock" />
                    {new Intl.DateTimeFormat("zh-CN", { year: "numeric", month: "2-digit", day: "2-digit" }).format(new Date(post.date))}
                  </div>
                  <div className="post-meta">
                    {post.categories?.nodes?.map((category) => (
                      <span key={`category-${post.id}-${category.slug}`}>
                        <Link href={`/category/${category.slug}`}>{category.name}</Link>
                      </span>
                    ))}
                    {post.tags?.nodes?.slice(0, 3).map((tag) => (
                      <span key={`tag-${post.id}-${tag.slug}`}>
                        <Link href={`/tag/${tag.slug}`}>{tag.name}</Link>
                      </span>
                    ))}
                    {extras?.meta?.view_count ? (
                      <span>
                        <i className="fa-regular fa-eye" />
                        {`${extras.meta.view_count} 次阅读`}
                      </span>
                    ) : null}
                    {typeof extras?.meta?.comment_count === "number" ? (
                      <span>
                        <i className="fa-regular fa-comment" />
                        {`${extras.meta.comment_count} 条评论`}
                      </span>
                    ) : null}
                  </div>
                  <div className="post-title">
                    <Link href={post.uri}>
                      <h3>{post.title}</h3>
                    </Link>
                  </div>
                  <div className="post-excerpt">
                    <div className="ai-excerpt-tip">
                      <i className={extras?.ai_excerpt ? "fa-solid fa-atom" : "fa-solid fa-bars-staggered"} />
                      {extras?.ai_excerpt ? "AI Excerpt" : "Excerpt"}
                    </div>
                    <p>{excerpt}</p>
                  </div>
                </article>
              );
            })}
          </main>
        </div>
      ) : null}
    </div>
  );
}
