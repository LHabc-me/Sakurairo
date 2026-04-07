import type { Metadata } from "next";
import type { ReactNode } from "react";

import "./globals.css";

import { SiteShell } from "@/components/site-shell";
import { getSiteConfig } from "@/lib/wordpress";

export const dynamic = "force-dynamic";

export async function generateMetadata(): Promise<Metadata> {
  const site = await getSiteConfig();
  return {
    title: {
      default: site.site.name,
      template: `%s | ${site.site.name}`
    },
    description: site.site.description,
    icons: site.branding.favicon || site.site.icon || undefined,
    metadataBase: new URL(site.site.url)
  };
}

export default async function RootLayout({
  children
}: Readonly<{
  children: ReactNode;
}>) {
  const site = await getSiteConfig();
  const themeStylesheetUrl = new URL("wp-content/themes/Shinonomeiro/style.css", site.site.url).toString();

  return (
    <html lang={site.site.language || "zh-CN"}>
      <head>
        <link rel="stylesheet" href="https://s4.zstatic.net/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Serif+SC|Noto+Sans+SC|Fira+Code&display=swap" />
        <link rel="stylesheet" href={`${themeStylesheetUrl}?ver=${site.compat.theme_version || "latest"}`} />
        {site.theme.custom_css ? <style dangerouslySetInnerHTML={{ __html: site.theme.custom_css }} /> : null}
      </head>
      <body>
        <SiteShell site={site}>{children}</SiteShell>
      </body>
    </html>
  );
}
