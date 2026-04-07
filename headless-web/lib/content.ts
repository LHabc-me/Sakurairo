export type TocItem = {
  id: string;
  text: string;
  level: number;
};

export function extractToc(html: string): TocItem[] {
  const regex = /<h([1-6])([^>]*)>(.*?)<\/h\1>/gis;
  const items: TocItem[] = [];
  let match: RegExpExecArray | null;

  while ((match = regex.exec(html)) !== null) {
    const level = Number(match[1]);
    const attrs = match[2] || "";
    const rawContent = match[3] || "";
    const text = stripHtml(rawContent).trim();
    if (!text) {
      continue;
    }

    const idMatch = attrs.match(/id=["']([^"']+)["']/i);
    const id = idMatch?.[1] || slugify(text);
    items.push({ id, text, level });
  }

  return items;
}

export function stripHtml(value: string): string {
  return value.replace(/<[^>]+>/g, " ");
}

export function slugify(value: string): string {
  return value
    .toLowerCase()
    .replace(/[^\p{L}\p{N}\s-]/gu, "")
    .trim()
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-");
}

export function formatDate(date: string | null | undefined): string {
  if (!date) {
    return "";
  }

  return new Intl.DateTimeFormat("zh-CN", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit"
  }).format(new Date(date));
}
