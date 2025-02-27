T = int(input())
for _ in range(T):
    N = int(input())
    names = [input().strip() for _ in range(N)] 
    print(f"Hello {', '.join(names)}")