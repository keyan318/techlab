// Which command dialect the device consoles speak. Upstream NetSim is Linux;
// TechLab embeds switch every device to Windows (see embed.ts).
export type ShellOs = 'linux' | 'windows';

let current: ShellOs = 'linux';

export function shellOs(): ShellOs {
  return current;
}

export function setShellOs(os: ShellOs): void {
  current = os;
}
