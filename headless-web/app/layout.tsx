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

  return (
    <html lang={site.site.language || "zh-CN"}>
      <body>
        <SiteShell site={site}>{children}</SiteShell>
      </body>
    </html>
  );
}
