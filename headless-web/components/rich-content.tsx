type RichContentProps = {
  html: string;
};

export function RichContent({ html }: RichContentProps) {
  return (
    <div
      className="prose prose-stone max-w-none prose-headings:tracking-[-0.03em] prose-pre:rounded-2xl prose-pre:bg-[#2c231f] prose-pre:text-stone-100 prose-a:text-[color:var(--accent-strong)] prose-img:rounded-[1.25rem]"
      dangerouslySetInnerHTML={{ __html: html }}
    />
  );
}
