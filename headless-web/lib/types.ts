export type SiteConfig = {
  generated_at: string;
  site: {
    name: string;
    description: string;
    language: string;
    url: string;
    icon: string;
  };
  branding: {
    logo: string;
    text_logo: {
      text: string;
      font_name: string;
    };
    favicon: string;
  };
  theme: {
    skin: string;
    skin_matching: string;
    skin_dark: string;
    custom_css: string;
  };
  footer: {
    info: string;
    show_hitokoto: boolean;
    show_load_stats: boolean;
    show_upyun: boolean;
    show_sakura_icon: boolean;
    addition_html: string;
  };
  injections: {
    head_html: string;
  };
  menus: {
    primary: MenuItem[];
  };
  social_links: SocialLink[];
  compat: {
    options_key: string;
    legacy_options_key: string;
    theme_version: string;
  };
};

export type HomepageConfig = {
  generated_at: string;
  components: Array<"exhibition" | "primary" | "static_page" | string>;
  display_area: {
    title: string;
    icon: string;
  };
  post_area: {
    title: string;
    icon: string;
  };
  capsule_components: string[];
  capsules: HomepageCapsule[];
  exhibition_items: HomepageExhibitionItem[];
  static_page: {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    uri: string;
  } | null;
};

export type HomepageCapsule = {
  id: string;
  type: "stat" | "announcement" | "link" | string;
  label: string;
  icon: string;
  value: string;
  link?: {
    name: string;
    url: string;
    image: string;
    description: string;
  };
};

export type HomepageExhibitionItem = {
  id: string;
  title: string;
  description: string;
  link: string;
  image: string;
};

export type MenuItem = {
  id: number;
  title: string;
  url: string;
  target: string;
  parent: number;
  order: number;
};

export type SocialLink = {
  id: string;
  label: string;
  url: string;
  icon?: string;
};

export type SimplePostCard = {
  id: string;
  databaseId: number;
  title: string;
  slug: string;
  uri: string;
  excerpt: string;
  date: string;
  modified: string;
  featuredImage?: {
    node?: {
      sourceUrl?: string | null;
      altText?: string | null;
    } | null;
  } | null;
  categories?: {
    nodes: Array<{ name: string; slug: string }>;
  } | null;
  tags?: {
    nodes: Array<{ name: string; slug: string }>;
  } | null;
};

export type PostExtras = {
  id: number;
  type: string;
  ai_excerpt: string;
  annotations: Array<{ title?: string; content?: string }>;
  cover: string;
  reading: {
    word_count: number;
    has_toc: boolean;
  };
  rendered: {
    content: string;
    excerpt: string;
  };
  navigation?: {
    previous?: {
      id: number;
      title: string;
      uri: string;
    } | null;
    next?: {
      id: number;
      title: string;
      uri: string;
    } | null;
  };
  meta?: {
    comment_count: number;
    view_count: number;
  };
};

export type ContentNode =
  | {
      __typename: "Post";
      databaseId: number;
      title: string;
      slug: string;
      uri: string;
      date: string;
      modified: string;
      excerpt: string;
      content: string;
      featuredImage?: {
        node?: {
          sourceUrl?: string | null;
          altText?: string | null;
        } | null;
      } | null;
      categories?: {
        nodes: Array<{ name: string; slug: string }>;
      } | null;
      tags?: {
        nodes: Array<{ name: string; slug: string }>;
      } | null;
      author?: {
        node?: {
          name?: string | null;
          slug?: string | null;
        } | null;
      } | null;
    }
  | {
      __typename: "Page";
      databaseId: number;
      title: string;
      slug: string;
      uri: string;
      date?: string | null;
      modified?: string | null;
      excerpt?: string | null;
      content?: string | null;
      featuredImage?: {
        node?: {
          sourceUrl?: string | null;
          altText?: string | null;
        } | null;
      } | null;
    };
