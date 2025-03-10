import csv
import random

# Define room types
room_types = [
    (1, "Standard Room", "Basic and comfortable", "Single Room"),
    (2, "Standard Room", "Basic with double bed", "Double Room"),
    (3, "Standard Room", "Two separate single beds", "Twin Room"),
    (4, "Luxury & Premium", "Spacious room with extra amenities", "Deluxe Room"),
    (5, "Luxury & Premium", "Includes a separate living area", "Suite"),
    (6, "Luxury & Premium", "For business travelers", "Executive Suite"),
    (7, "Luxury & Premium", "Top luxury experience", "Presidential Suite"),
    (8, "Specialty Rooms", "Ideal for families", "Family Room"),
    (9, "Specialty Rooms", "Two connected rooms", "Connecting Rooms"),
    (10, "Specialty Rooms", "Designed for accessibility", "Accessible Room"),
    (11, "Specialty Rooms", "Romantic getaway", "Honeymoon Suite"),
]

# Write room_types.csv
with open("room_types.csv", "w", newline="") as file:
    writer = csv.writer(file)
    writer.writerow(["id", "room_category", "description", "room_type"])
    writer.writerows(room_types)

# Generate rooms data
rooms = []
room_id = 1

for floor in range(1, 11):  # 10 floors
    for room in range(1, 51):  # 50 rooms per floor
        room_number = int(f"{floor}{room:02}")  # Format: 101, 102, ..., 150, 201, ...
        capacity = random.choice([1, 2, 3, 4])  # Random capacity
        price_per_night = random.randint(50, 500)  # Random pricing
        status = 0  # Default status (0 = available)
        room_type_id = random.randint(1, len(room_types))  # Assign random room type
        rooms.append([room_id, floor, room_number, capacity, price_per_night, status, room_type_id])
        room_id += 1

# Write rooms.csv
with open("rooms.csv", "w", newline="") as file:
    writer = csv.writer(file)
    writer.writerow(["id", "floor", "room_number", "capacity", "price_per_night", "status", "room_type_id"])
    writer.writerows(rooms)

print("CSV files generated successfully!")
