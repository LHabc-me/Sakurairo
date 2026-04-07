import Link from "next/link";

export default function NotFound() {
  return (
    <section className="rounded-[2rem] border border-white/60 bg-white/72 px-8 py-20 text-center shadow-[0_18px_60px_rgba(110,78,56,0.08)]">
      <div className="text-[0.72rem] uppercase tracking-[0.35em] text-stone-500">404</div>
      <h1 className="mt-4 text-4xl font-semibold tracking-[-0.04em] text-stone-950">页面不存在</h1>
      <p className="mx-auto mt-5 max-w-xl text-sm leading-7 text-stone-600">当前路径没有匹配到 WordPress 内容节点，或者对应内容尚未发布。</p>
      <Link href="/" className="mt-8 inline-flex rounded-full border border-stone-300 px-5 py-3 text-xs uppercase tracking-[0.25em] text-stone-700">
        返回首页
      </Link>
    </section>
  );
}
