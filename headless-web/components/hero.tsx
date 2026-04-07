type HeroProps = {
  eyebrow: string;
  title: string;
  description: string;
  accent?: string;
};

export function Hero({ eyebrow, title, description, accent = "Shinonomeiro" }: HeroProps) {
  return (
    <section className="relative overflow-hidden rounded-[2rem] border border-white/60 bg-[linear-gradient(145deg,_rgba(255,255,255,0.86),_rgba(255,247,240,0.64))] px-6 py-10 shadow-[0_20px_80px_rgba(110,78,56,0.12)] sm:px-10 sm:py-14 lg:px-14">
      <div className="absolute inset-y-0 right-0 hidden w-[36%] bg-[radial-gradient(circle_at_40%_20%,_rgba(211,122,93,0.28),_transparent_35%),radial-gradient(circle_at_70%_70%,_rgba(61,41,33,0.16),_transparent_45%)] lg:block" />
      <div className="relative max-w-3xl">
        <div className="mb-4 text-[0.72rem] uppercase tracking-[0.35em] text-stone-500">{eyebrow}</div>
        <h1 className="max-w-4xl text-4xl font-semibold tracking-[-0.04em] text-stone-950 sm:text-5xl lg:text-6xl">{title}</h1>
        <p className="mt-5 max-w-2xl text-base leading-8 text-stone-700 sm:text-lg">{description}</p>
        <div className="mt-8 inline-flex items-center gap-3 rounded-full border border-stone-300 bg-white/70 px-4 py-2 text-xs uppercase tracking-[0.25em] text-stone-600">
          <span className="inline-block h-2 w-2 rounded-full bg-[color:var(--accent)]" />
          {accent}
        </div>
      </div>
    </section>
  );
}
