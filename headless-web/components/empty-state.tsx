type EmptyStateProps = {
  title: string;
  description: string;
};

export function EmptyState({ title, description }: EmptyStateProps) {
  return (
    <section className="rounded-[2rem] border border-dashed border-stone-300 bg-white/55 px-6 py-14 text-center">
      <h2 className="text-2xl font-semibold tracking-[-0.03em] text-stone-900">{title}</h2>
      <p className="mx-auto mt-4 max-w-xl text-sm leading-7 text-stone-600">{description}</p>
    </section>
  );
}
